<?php

namespace Omekalia\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class OmekaliaController extends AbstractActionController
{
    protected $omekaliaStatus;

    protected $moduleManager;

    protected $themeManager;

    public function __construct($omekaliaStatus, $moduleManager, $themeManager)
    {
        $this->omekaliaStatus = $omekaliaStatus;
        $this->moduleManager = $moduleManager;
        $this->themeManager = $themeManager;
    }

    public function statusAction()
    {
        $omekaliaData = $this->omekaliaStatus->getOmekaliaData();

        $modules = $this->moduleManager->getModules();
        $omekaliaModuleNames = array_map(fn($module) => $module['name'], $omekaliaData['modules']);
        $omekaliaModuleNames[] = 'Omekalia';

        $otherModules = array_filter($modules, fn($module) => false === array_search($module->getId(), $omekaliaModuleNames));
        $otherActiveModules = array_filter($otherModules, fn($module) => $module->getState() === 'active');
        $otherInactiveModules = array_filter($otherModules, fn($module) => $module->getState() !== 'active');

        $themes = $this->themeManager->getThemes();
        $omekaliaThemeNames = array_map(fn($theme) => $theme['name'], $omekaliaData['themes']);
        $otherThemes = array_filter($themes, fn($theme) => false === array_search($theme->getId(), $omekaliaThemeNames));

        $view = new ViewModel;
        $view->setVariable('omekaliaData', $omekaliaData);
        $view->setVariable('otherActiveModules', $otherActiveModules);
        $view->setVariable('otherInactiveModules', $otherInactiveModules);
        $view->setVariable('otherThemes', $otherThemes);

        return $view;
    }
}
