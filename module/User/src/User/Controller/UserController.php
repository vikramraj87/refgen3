<?php
namespace User\Controller;

use User\Entity\User,
    User\Service\FacebookService;
use Zend\Mvc\Controller\AbstractActionController,
    Zend\Session\Container;

class UserController extends AbstractActionController
{
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
        /** @var FacebookService $facebookService */
        $facebookService = $this->getServiceLocator()->get('FacebookService');
        return array(
            'facebookLoginUrl' => $facebookService->getLoginUrl()
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
        /*
        $code = $this->params()->fromQuery('code', '');
        if(empty($code)) {
            /**
             * todo: Log the error from google unless its
             * access denied

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

        /** @var \Zend\Http\Response $response
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
*/
    }
}