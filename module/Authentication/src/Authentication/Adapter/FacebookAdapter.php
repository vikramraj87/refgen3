<?php
namespace Authentication\Adapter;

use Facebook\FacebookSession,
    Facebook\FacebookRequestException,
    Facebook\FacebookRequest,
    Facebook\FacebookResponse,
    Facebook\FacebookRedirectLoginHelper,
    Facebook\GraphUser;

use Zend\Authentication\Result;
class FacebookAdapter extends AbstractAdapter
{
    protected $id = 'Facebook';

    /** @var FacebookRedirectLoginHelper */
    private $helper;

    public function init()
    {
        FacebookSession::setDefaultApplication($this->clientId, $this->clientSecret);
    }

    /**
     * Cannot initialize the $this->loginUrl in the URL. The state differs. Dont know why. So overriding the
     * getLoginUrl
     *
     * @return string
     */
    public function getLoginUrl()
    {
        $loginUrl = $this->helper()->getLoginUrl($this->scopes);
        return $loginUrl;
    }

    public function getRerequestUrl()
    {
        return $this->getLoginUrl() . '&auth_type=rerequest';
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        $helper = $this->helper();
        $session = $helper->getSessionFromRedirect();

        if(null === $session) {
            $msg = isset($_GET['error_reason']) ? $_GET['error_reason'] : '';
            throw new \Zend\Authentication\Adapter\Exception\UnexpectedValueException($msg);
        }

        $permissions = $this->permissions($session);
        if(!isset($permissions['email']) || $permissions['email'] === false) {
            return new Result(Result::FAILURE_IDENTITY_AMBIGUOUS, null);
        }

        $user = $this->profile($session);
        return new Result(Result::SUCCESS, $this->identityFromData($user));
    }

    private function profile(FacebookSession $session)
    {
        /** @var FacebookRequest $request */
        $request  = new FacebookRequest($session, 'GET', '/me');

        /** @var FacebookResponse $response */
        $response = $request->execute();

        /** @var GraphUser $userData */
        $userData = $response->getGraphObject('Facebook\GraphUser');

        $profile = array(
            'socialId'   => $userData->getId(),
            'email'      => $userData->getProperty('email'),
            'firstName'  => $userData->getFirstName(),
            'middleName' => null === $userData->getMiddleName() ? '' : $userData->getMiddleName(),
            'lastName'   => $userData->getLastName(),
            'name'       => $userData->getName()
        );

        $request = new FacebookRequest(
            $session,
            'GET',
            '/me/picture',
            array(
                'redirect' => false,
                'type'     => 'square'
            )
        );
        $response = $request->execute();
        $pictureData = $response->getResponse()->data;
        $profile['picture'] = array(
            'link'         => $pictureData->url,
            'isSilhouette' => $pictureData->is_silhouette
        );
        return $profile;
    }

    private function permissions(FacebookSession $session)
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

    private function helper()
    {
        if(null === $this->helper) {
            $this->helper = new FacebookRedirectLoginHelper($this->redirectUrl);
        }
        return $this->helper;
    }
} 