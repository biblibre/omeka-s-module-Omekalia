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

        $controller = new OmekaliaController($omekaliaStatus);

        return $controller;
    }
}
