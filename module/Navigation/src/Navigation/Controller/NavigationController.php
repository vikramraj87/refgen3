<?php
namespace Navigation\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class NavigationController extends AbstractActionController
{
    public function navigationAction()
    {
        $view = new ViewModel();
        $view->setTemplate('partials/topnav');
        $view->setTerminal(true);
        return $view;
    }
} 