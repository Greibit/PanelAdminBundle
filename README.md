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
Dependencies
------------
* KnpMenuBundle


