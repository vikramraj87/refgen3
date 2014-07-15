<?php
namespace Authentication\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Authentication\Adapter\FacebookAdapter,
    Authentication\Adapter\GoogleAdapter,
    Authentication\Service\AuthenticationService;
use Zend\Authentication\Result;
class AuthenticationController extends AbstractActionController
{
    public function facebookAction()
    {
        /** @var FacebookAdapter $adapter */
        $adapter = $this->getServiceLocator()->get('Authentication\Adapter\Facebook');

        /** @var AuthenticationService $authService */
        $authService = $this->getServiceLocator()->get('Authentication\Service\Authentication');

        /** @var Result $result */
        $result = $authService->authenticate($adapter);
        if($result->isValid()) {
            $this->redirect()->toRoute('home');
        }
    }

    public function googleAction()
    {
        /** @var GoogleAdapter $adapter */
        $adapter = $this->getServiceLocator()->get('Authentication\Adapter\Google');
        $adapter->setCode($this->params()->fromQuery('code', null));

        /** @var AuthenticationService $authService */
        $authService = $this->getServiceLocator()->get('Authentication\Service\Authentication');

        /** @var Result $result */
        $result = $authService->authenticate($adapter);
        if($result->isValid()) {
            $this->redirect()->toRoute('home');
        }
    }
} 