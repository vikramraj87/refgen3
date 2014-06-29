<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use User\Service\FacebookService;

class AuthController extends AbstractActionController
{
    public function facebookAction()
    {
        /** @var FacebookService $service */
        $service    = $this->getServiceLocator()->get('FacebookService');
        $result     = $service->authenticate();
        if($result) {
            return $this->redirect()->toRoute('user');
        }
    }

    public function facebookEmailAction()
    {
        /** @var FacebookService $service */
        $service    = $this->getServiceLocator()->get('FacebookService');

        $reLoginUrl = $service->getRerequestUrl();
        return array(
            'reLoginUrl' => $reLoginUrl,
        );
    }

    public function googleAction()
    {

    }
} 