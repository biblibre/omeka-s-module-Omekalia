<?php

namespace Omekalia\Service\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Omekalia\View\Helper\Omekalia;

class OmekaliaFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedNamed, array $options = null)
    {
        $moduleManager = $services->get('Omeka\ModuleManager');
        $themeManager = $services->get('Omeka\Site\ThemeManager');

        $viewHelper = new Omekalia($moduleManager, $themeManager);

        return $viewHelper;
    }
}
