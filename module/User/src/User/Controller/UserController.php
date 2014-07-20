<?php
namespace User\Controller;

use Authentication\Adapter\FacebookAdapter;
use Zend\Mvc\Controller\AbstractActionController,
    Zend\Session\Container;
use Zend\View\Model\ViewModel;


class UserController extends AbstractActionController
{
    public function loginAction()
    {
        /** @var FacebookAdapter $facebookAdapter */
        $facebookAdapter = $this->getServiceLocator()->get('Authentication\Adapter\Facebook');
        $googleAdapter   = $this->getServiceLocator()->get('Authentication\Adapter\Google');
        return array(
            'fbLoginUrl'     => $facebookAdapter->getLoginUrl(),
            'googleLoginUrl' => $googleAdapter->getLoginUrl()
        );
    }

    public function loginEmailRequiredAction()
    {
        /** @var FacebookAdapter $facebookAdapter */
        $facebookAdapter = $this->getServiceLocator()->get('Authentication\Adapter\Facebook');
        $googleAdapter   = $this->getServiceLocator()->get('Authentication\Adapter\Google');
        $view = new ViewModel();
        $view->setTemplate('user/user/login');
        $view->setVariable('fbLoginUrl', $facebookAdapter->getRerequestUrl());
        $view->setVariable('googleLoginUrl', $googleAdapter->getLoginUrl());
        $view->setVariable('emailRequired', true);
        return $view;
    }

    public function logoutAction()
    {
        /** @var \Authentication\Service\AuthenticationService $service */
        $service = $this->getServiceLocator()->get('Authentication\Service\Authentication');
        $service->clearIdentity();

        /** @var \Collection\Service\CollectionService $collectionService */
        $collectionService = $this->getServiceLocator()->get('Collection\Service\Collection');
        $collectionService->setCollection(null);

        return $this->redirect()->toRoute('home');
    }
}