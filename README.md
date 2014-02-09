PanelAdminBundle
================

*This Repository is UNDER DEVELOPMENT, it's not recommended to use it*

PanelAdminBundle integrates a [Admin Panel template](http://startbootstrap.com/sb-admin-v2) in your Symfony Project.

Installation
------------
Add the bundle to the `composer.json` of your project:
```json
{
   "require": {
        "greibit/panel-admin-bundle": "dev-master"
    }
}
```
Enable the bundle in the `AppKernel.php` of your Symfony project:

```php
// app/AppKernel.php
//...
class AppKernel extends Kernel
{
    //...
    public function registerBundles()
    {
        $bundles = array(
            ...
            new Greibit\Bundle\PanelAdminBundle\GreibitPanelAdminBundle()
        );
        //...

        return $bundles;
    }
    //...
}
```

Configuration
-------------
Add configuration to your config.yml:
```yaml
greibit_panel_admin:
    menu_builder: "AcmeDemoBundle:Builder:sidebarMenu"
    panel_title: "Greibit Administration"
```

Use it!
-------------
To use the Admin Panel proposed by Greibit just extends your twig templates with the GreibitPanelAdmin `layout` and use the `page` block

``` twig

{% extends 'GreibitPanelAdminBundle::layout.html.twig' %}
{% block page %} {% endblock %}

```

Dependencies
------------
* KnpMenuBundle

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/Greibit/paneladminbundle/trend.png)](https://bitdeli.com/free "Bitdeli Badge")


