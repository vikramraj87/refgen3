<?php
namespace User\Service;

use Facebook\FacebookSession,
    Facebook\FacebookRequest,
    Facebook\FacebookRequestException,
    Facebook\FacebookResponse,
    Facebook\FacebookRedirectLoginHelper,
    Facebook\GraphObject,
    Facebook\GraphUser;
use User\Entity\User;


class FacebookService extends AbstractAuthProvider
{
    const ERROR_EMAIL_DENIED      = 'email_denied';
    const ERROR_ACQUIRING_SESSION = 'session_not_acquired';
    const USER_DENIED             = 'user_denied';

    /** @var FacebookRedirectLoginHelper */
    private $helper;

    private $logoutUrl;

    protected function init()
    {
        FacebookSession::setDefaultApplication($this->clientId, $this->clientSecret);
    }

    public function getLoginUrl()
    {
        $helper = $this->getHelper();
        $loginUrl = $helper->getLoginUrl($this->scopes);
        return $loginUrl;
    }

    public function getRerequestUrl()
    {
        return $this->getLoginUrl() . '&auth_type=rerequest';
    }

    public function authenticate()
    {
        $helper  = $this->getHelper();
        $session = $helper->getSessionFromRedirect();

        if(null === $session) {
            if(isset($_GET['error_reason'])) {
                $this->error = $_GET['error_reason'];
            } else {
                $this->error = self::ERROR_ACQUIRING_SESSION;
            }
            return false;
        }

        $permissions = $this->getPermissions($session);
        if(!isset($permissions['email'])
            || $permissions['email'] === false) {
            $this->error = self::ERROR_EMAIL_DENIED;
            return false;
        }

        $user = $this->getProfile($session);
        return true;
    }

    private function getProfile(FacebookSession $session)
    {
        /** @var FacebookRequest $request */
        $request  = new FacebookRequest($session, 'GET', '/me');

        /** @var FacebookResponse $response */
        $response = $request->execute();

        /** @var GraphUser $user */
        $userData = $response->getGraphObject('Facebook\GraphUser');

        $user = new User();
        $user->setName($userData->getName());
        $user->setEmail($userData->getProperty('email'));
        $user->setFacebookId($userData->getId());
        return $this->persist($user);
    }

    private function getPermissions(FacebookSession $session)
    {
        /** @var FacebookRequest $request */
        $request  = new FacebookRequest($session, 'GET', '/me/permissions');

        /** @var FacebookResponse $response */
        $response = $request->execute();

        $raw = $response->getResponse();
        $permissions = array();
        foreach($raw->data as $permission) {
            $status = 'granted' === $permission->status ? true : false;
            $permissions[$permission->permission] = $status;
        }
        return $permissions;
    }

    private function getHelper()
    {
        if(null === $this->helper) {
            $this->helper = new FacebookRedirectLoginHelper($this->redirectUrl);
        }
        return $this->helper;
    }
}