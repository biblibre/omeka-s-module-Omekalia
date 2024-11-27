<?php

namespace Omekalia;

use Omeka\Module\AbstractModule;
use Laminas\Mvc\MvcEvent;

class Module extends AbstractModule
{
    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);

        $acl = $this->getServiceLocator()->get('Omeka\Acl');
        $acl->deny('site_admin', 'Omekalia\Controller\Admin\Omekalia');
    }

    public function getConfig()
    {
        return require __DIR__ . '/config/module.config.php';
    }
}
