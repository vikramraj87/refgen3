<?php
namespace User\Controller;

include_once 'Google/Client.php';

use Google_Client;
use User\Entity\User;
use Zend\Http\Header\Authorization,
    Zend\Mvc\Controller\AbstractActionController,
    Zend\Http\Client,
    Zend\Http\Response,
    Zend\Http\Request,
    Zend\Session\Container;
use Facebook\FacebookSession,
    Facebook\FacebookRedirectLoginHelper,
    Facebook\FacebookRequestException,
    Facebook\GraphUser,
    Facebook\FacebookRequest;

class UserController extends AbstractActionController
{
    /** @var \Zend\Http\Client */
    private $httpClient;

    /** @var Google_Client */
    private $googleClient;

    public function indexAction()
    {
        $userContainer = new Container('user');
        if(!$userContainer->offsetExists('user')) {
            return $this->redirect()->toRoute('user/login');
        }
        return array(
            'user' => $userContainer->user
        );
    }

    public function loginAction()
    {
        $this->initFacebookSession();

        $config = $this->getServiceLocator()->get('config');
        $fbConfig = $config['facebook'];
        $fbHelper = new FacebookRedirectLoginHelper($fbConfig['redirect_uri']);

        return array(
            'googleUrl' => $this->googleClient->createAuthUrl(),
            'fbUrl'     => $fbHelper->getLoginUrl(array(
                    'scope' => $fbConfig['scopes']
                )
            )
        );
    }

    public function logoutAction()
    {
        $userContainer = new Container('user');
        unset($userContainer->user);
        return $this->redirect()->toRoute('home');
    }

    public function googleAuthAction()
    {
        $code = $this->params()->fromQuery('code', '');
        if(empty($code)) {
            /**
             * todo: Log the error from google unless its
             * access denied
             */
            return $this->redirect()->toRoute('user/login');
        }

        $this->googleClient->authenticate($code);
        $accessTokenData = json_decode($this->googleClient->getAccessToken(), true);
        $accessToken = $accessTokenData['access_token'];

        $request = new Request();
        $request->setUri('https://www.googleapis.com/oauth2/v2/userinfo');
        $request->getHeaders()->addHeader(new Authorization('Bearer ' . $accessToken));
        $httpClient = $this->getHttpClient();
        $httpClient->setRequest($request);

        /** @var \Zend\Http\Response $response */
        $response = $httpClient->send();
        $userData = json_decode($response->getBody(), true);

        $user = new User();
        $user->setEmail($userData['email']);
        $user->setFirstName($userData['given_name']);
        $user->setLastName($userData['family_name']);
        $user->setName($userData['name']);

        $userContainer = new Container('user');
        $userContainer->user = $user;
        return $this->redirect()->toRoute('user');
    }

    public function fbAuthAction()
    {
        $this->initFacebookSession();
        $config = $this->getServiceLocator()->get('config');
        $fbConfig = $config['facebook'];
        $helper = new FacebookRedirectLoginHelper($fbConfig['redirect_uri']);
        try {
            $session = $helper->getSessionFromRedirect();
        } catch (FacebookRequestException $e) {
            // todo: handle the exception
        } catch (\Exception $e) {
            // todo: handle the exception
        }
        if($session) {
            $request = new FacebookRequest($session, 'GET', '/me');
            $response = $request->execute();
            /** @var GraphUser $graphObject */
            $graphObject = $response->getGraphObject(GraphUser::className());

            $email = $graphObject->getProperty('email');
            if(empty($email)) {
                $id = $graphObject->getId();
                var_dump($id);
                //todo: Check facebook id
            }
            /*
            $user = new User();
            $user->setFirstName($graphObject->getFirstName());
            $user->setLastName($graphObject->getLastName());
            $user->setName($graphObject->getName());
            $user->setEmail($email);

            $userContainer = new Container('user');
            $userContainer->user = $user; */
        }
        //return $this->redirect()->toRoute('user');
    }

    /**
     * @param \Google_Client $googleClient
     */
    public function setGoogleClient($googleClient)
    {
        $this->googleClient = $googleClient;
    }

    private function checkFacebookId()
    {

    }

    /**
     * Inits facebook session with app_id and app_secret
     */
    private function initFacebookSession()
    {
        $sm = $this->getServiceLocator();
        $config = $sm->get('config');
        $fbConfig = $config['facebook'];
        FacebookSession::setDefaultApplication($fbConfig['app_id'], $fbConfig['app_secret']);
    }

    /**
     * @return \Zend\Http\Client
     */
    private function getHttpClient()
    {
        if($this->httpClient == null) {
            $this->httpClient = new Client();
            $this->httpClient->setMethod(Request::METHOD_GET);
        }
        return $this->httpClient;
    }

    /**
     * Returns the body of the the request or null
     *
     * @param string $uri
     * @return null|string
     */
    private function doRequest($uri = '')
    {
        /** @var \Zend\Http\Client $client */
        $client = $this->getHttpClient();
        $client->setUri($uri);

        /** @var Response $response */
        $response = $client->send();
        if(!$response->isSuccess()) {
            /**
             * todo: handle the failure response
             */
            return null;
        }
        return $response->getBody();
    }
} 