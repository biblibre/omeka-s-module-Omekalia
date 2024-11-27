<?php

namespace Omekalia\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class OmekaliaController extends AbstractActionController
{
    protected $omekaliaStatus;

    public function __construct($omekaliaStatus)
    {
        $this->omekaliaStatus = $omekaliaStatus;
    }

    public function statusAction()
    {
        $view = new ViewModel;
        $view->setVariable('omekaliaData', $this->omekaliaStatus->getOmekaliaData());

        return $view;
    }
}
