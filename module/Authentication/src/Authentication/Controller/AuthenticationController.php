<?php
namespace Authentication\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Authentication\Adapter\FacebookAdapter,
    Authentication\Adapter\GoogleAdapter,
    Authentication\Service\AuthenticationService,
    Collection\Service\CollectionService;
use Zend\Authentication\Result;
class AuthenticationController extends AbstractActionController
{
    /** @var FacebookAdapter */
    private $facebookAdapter;

    /** @var GoogleAdapter */
    private $googleAdapter;

    /** @var AuthenticationService */
    private $authService;

    public function loginAction()
    {
        $reason = $this->params()->fromRoute('reason', '');
        $emailRequired = false;
        $fbLoginUrl = $this->facebookAdapter->getLoginUrl();
        if('email-required' == $reason) {
            $emailRequired = true;
            $fbLoginUrl = $this->facebookAdapter->getRerequestUrl();
        }
        return array(
            'fbLoginUrl' => $fbLoginUrl,
            'googleLoginUrl' => $this->googleAdapter->getLoginUrl(),
            'emailRequired' => $emailRequired
        );
    }

    public function logoutAction()
    {
        // todo: Find a better solution for setting collection service to null
        /** @var CollectionService $collectionService */
        $collectionService = $this->getServiceLocator()->get('Collection\Service\Collection');
        $collectionService->setCollection(null);

        $this->authService->clearIdentity();

        return $this->redirect()->toRoute('home');
    }

    public function facebookAction()
    {
        $error = $this->params()->fromQuery('error', '');
        switch($error) {
            case 'access_denied':
                $this->flashMessenger()->addErrorMessage('Access denied by the user');
                $this->redirect()->toRoute('auth/login', array('reason' => 'access-denied'));
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
                $this->redirect()->toRoute('auth/login', array('reason' => 'email-required'));
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
                $this->redirect()->toRoute('auth/login',array('reason' => 'access-denied'));
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

    /**
     * @param \Authentication\Service\AuthenticationService $authService
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @param \Authentication\Adapter\FacebookAdapter $facebookAdapter
     */
    public function setFacebookAdapter(FacebookAdapter $facebookAdapter)
    {
        $this->facebookAdapter = $facebookAdapter;
    }

    /**
     * @param \Authentication\Adapter\GoogleAdapter $googleAdapter
     */
    public function setGoogleAdapter(GoogleAdapter $googleAdapter)
    {
        $this->googleAdapter = $googleAdapter;
    }


} 