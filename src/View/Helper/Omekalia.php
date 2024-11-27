<?php

namespace Omekalia\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class Omekalia extends AbstractHelper
{
    protected $moduleManager;

    protected $themeManager;

    public function __construct($moduleManager, $themeManager)
    {
        $this->moduleManager = $moduleManager;
        $this->themeManager = $themeManager;
    }

    public function getModule($name)
    {
        return $this->moduleManager->getModule($name);
    }

    public function getTheme($name)
    {
        return $this->themeManager->getTheme($name);
    }
}
