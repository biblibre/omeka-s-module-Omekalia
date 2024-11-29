<?php
namespace Omekalia\Service\Controller\Admin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Omekalia\Controller\Admin\OmekaliaController;

class OmekaliaControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedNamed, array $options = null)
    {
        $omekaliaStatus = $services->get('Omekalia\Status');
        $moduleManager = $services->get('Omeka\ModuleManager');
        $themeManager = $services->get('Omeka\Site\ThemeManager');

        $controller = new OmekaliaController($omekaliaStatus, $moduleManager, $themeManager);

        return $controller;
    }
}
