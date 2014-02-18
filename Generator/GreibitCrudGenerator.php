<?php
/*
 * This file is part of the CrudGeneratorBundle
 *
 * It is based/extended from SensioGeneratorBundle
 *
 * (c) Jordi Llonch <llonch.jordi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Greibit\Bundle\PanelAdminBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class GreibitCrudGenerator extends DoctrineCrudGenerator
{
    protected $formFilterGenerator;
    protected $action_icons;

    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite)
    {
        $this->routePrefix = $routePrefix;
        $this->routeNamePrefix = str_replace('/', '_', $routePrefix);
        $this->actions = $needWriteActions ? array('index', 'show', 'new', 'edit', 'delete') : array('index', 'show');
        $this->action_icons = array(
            "show" => "search",
            "edit" => "pencil",
            "new" => "star",
            "delete" => "trash",
            "index" => "align-justify",
        );

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The CRUD generator does not support entity classes with multiple primary keys.');
        }

        if (!in_array('id', $metadata->identifier)) {
            throw new \RuntimeException('The CRUD generator expects the entity object has a primary key field named "id" with a getId() method.');
        }

        $this->entity   = $entity;
        $this->bundle   = $bundle;
        $this->metadata = $metadata;
        $this->setFormat($format);

        $this->generateControllerClass($forceOverwrite);

        $dir = sprintf('%s/Resources/views/%s', $this->bundle->getPath(), str_replace('\\', '/', $this->entity));

        if (!file_exists($dir)) {
            $this->filesystem->mkdir($dir, 0777);
        }

        $this->generateIndexView($dir);

        if (in_array('show', $this->actions)) {
            $this->generateShowView($dir);
        }

        if (in_array('new', $this->actions)) {
            $this->generateNewView($dir);
        }

        if (in_array('edit', $this->actions)) {
            $this->generateEditView($dir);
        }

        $this->generateTestClass();
        $this->generateConfiguration();

        try {
            $this->generateFormFilter($bundle, $entity, $metadata);
        } catch (\RuntimeException $e ) {
            // form already exists
        }
    }

    /**
     * Generates the entity form class if it does not exist.
     *
     * @param BundleInterface   $bundle   The bundle in which to create the class
     * @param string            $entity   The entity relative class name
     * @param ClassMetadataInfo $metadata The entity metadata class
     */
    public function generateFormFilter(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata)
    {
        $parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->className = $entityClass.'FilterType';
        $dirPath         = $bundle->getPath().'/Form';
        $this->classPath = $dirPath.'/'.str_replace('\\', '/', $entity).'FilterType.php';

        if (file_exists($this->classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s form class as it already exists under the %s file', $this->className, $this->classPath));
        }

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);

        $this->renderFile('form/FormFilterType.php.twig', $this->classPath, array(
            'fields_data'      => $this->getFieldsDataFromMetadata($metadata),
            'namespace'        => $bundle->getNamespace(),
            'entity_namespace' => implode('\\', $parts),
            'entity_class'     => $entityClass,
            'bundle'           => $bundle->getName(),
            'form_class'       => $this->className,
            'form_filter_type_name'   => strtolower(str_replace('\\', '_', $bundle->getNamespace()).($parts ? '_' : '').implode('_', $parts).'_'.$this->className),
        ));
    }

    public function getFilterType($dbType, $columnName)
    {
        switch ($dbType) {
            case 'boolean':
                return 'filter_choice';
            case 'datetime':
            case 'vardatetime':
            case 'datetimetz':
                return 'filter_date_range';
            case 'date':
                return 'filter_date_range';
                break;
            case 'decimal':
            case 'float':
            case 'integer':
            case 'bigint':
            case 'smallint':
                return 'filter_number_range';
                break;
            case 'string':
            case 'text':
                return 'filter_text';
                break;
            case 'time':
                return 'filter_text';
                break;
            case 'entity':
            case 'collection':
                return 'filter_entity';
                break;
            case 'array':
                throw new \Exception('The dbType "'.$dbType.'" is only for list implemented (column "'.$columnName.'")');
                break;
            case 'virtual':
                throw new \Exception('The dbType "'.$dbType.'" is only for list implemented (column "'.$columnName.'")');
                break;
            default:
                throw new \Exception('The dbType "'.$dbType.'" is not yet implemented (column "'.$columnName.'")');
                break;
        }
    }

    /**
     * Returns an array of fields data (name and filter widget to use).
     * Fields can be both column fields and association fields.
     *
     * @param ClassMetadataInfo $metadata
     * @return array $fields
     */
    private function getFieldsDataFromMetadata(ClassMetadataInfo $metadata)
    {
        $fieldsData = (array) $metadata->fieldMappings;

        // Convert type to filter widget
        foreach ($fieldsData as $fieldName => $data) {
            $fieldsData[$fieldName]['fieldName'] = $fieldName;
            $fieldsData[$fieldName]['filterWidget'] = $this->getFilterType($fieldsData[$fieldName]['type'], $fieldName);
        }

        return $fieldsData;
    }

    /**
     * Sets the configuration format.
     *
     * @param string $format The configuration format
     */
    private function setFormat($format)
    {
        switch ($format) {
            case 'yml':
            case 'xml':
            case 'php':
            case 'annotation':
                $this->format = $format;
                break;
            default:
                $this->format = 'yml';
                break;
        }
    }

    /**
     * Generates the index.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateIndexView($dir)
    {
        $this->renderFile('crud/views/index.html.twig.twig', $dir.'/index.html.twig', array(
                'bundle'            => $this->bundle->getName(),
                'entity'            => $this->entity,
                'fields'            => $this->metadata->fieldMappings,
                'actions'           => $this->actions,
                'record_actions'    => $this->getRecordActions(),
                'route_prefix'      => $this->routePrefix,
                'route_name_prefix' => $this->routeNamePrefix,
                'action_icons'      => $this->action_icons,
            ));
    }

    /**
     * Generates the show.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateShowView($dir)
    {
        $this->renderFile('crud/views/show.html.twig.twig', $dir.'/show.html.twig', array(
                'bundle'            => $this->bundle->getName(),
                'entity'            => $this->entity,
                'fields'            => $this->metadata->fieldMappings,
                'actions'           => $this->actions,
                'route_prefix'      => $this->routePrefix,
                'route_name_prefix' => $this->routeNamePrefix,
                'action_icons'      => $this->action_icons,
            ));
    }

    /**
     * Generates the new.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateNewView($dir)
    {
        $this->renderFile('crud/views/new.html.twig.twig', $dir.'/new.html.twig', array(
                'bundle'            => $this->bundle->getName(),
                'entity'            => $this->entity,
                'route_prefix'      => $this->routePrefix,
                'route_name_prefix' => $this->routeNamePrefix,
                'actions'           => $this->actions,
                'action_icons'      => $this->action_icons,
            ));
    }

    /**
     * Generates the edit.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateEditView($dir)
    {
        $this->renderFile('crud/views/edit.html.twig.twig', $dir.'/edit.html.twig', array(
                'route_prefix'      => $this->routePrefix,
                'route_name_prefix' => $this->routeNamePrefix,
                'entity'            => $this->entity,
                'bundle'            => $this->bundle->getName(),
                'actions'           => $this->actions,
                'action_icons'      => $this->action_icons,
            ));
    }
}