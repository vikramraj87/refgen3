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
        $error = $this->params()->fromQuery('error', '');
        switch($error) {
            case 'access_denied':
                $this->flashMessenger()->addErrorMessage('Access denied by the user');
                $this->redirect()->toRoute('user/login');
                break;
        }

        /** @var FacebookAdapter $adapter */
        $adapter = $this->getServiceLocator()->get('Authentication\Adapter\Facebook');

        /** @var AuthenticationService $authService */
        $authService = $this->getServiceLocator()->get('Authentication\Service\Authentication');

        /** @var Result $result */
        $result = $authService->authenticate($adapter);

        switch($result->getCode()) {
            case Result::FAILURE_IDENTITY_AMBIGUOUS:
                $this->redirect()->toRoute('user/login-email-required');
                break;
        }

        if($result->isValid()) {
            $this->redirect()->toRoute('home');
        }
    }

    public function googleAction()
    {
        $error = $this->params()->fromQuery('error', '');
        switch($error) {
            case 'access_denied':
                $this->flashMessenger()->addErrorMessage('Access denied by the user');
                $this->redirect()->toRoute('user/login');
                break;
        }

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