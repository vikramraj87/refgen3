<?php
namespace User\Entity;

use Facebook\FacebookSession,
    Facebook\FacebookRedirectLoginHelper,
    Facebook\FacebookRequestException,
    Facebook\FacebookRequest,
    Facebook\GraphUser;
class FacebookAuthStrategy extends AbstractAuthStrategy
{
    /**
     * @return \User\Entity\User|null
     */
    public function getUser()
    {
        FacebookSession::setDefaultApplication($this->clientId, $this->clientSecret);
        $helper = new FacebookRedirectLoginHelper($this->redirectUrl);

        try {
            $session = $helper->getSessionFromRedirect();
        } catch (FacebookRequestException $e) {
            // todo: handle the exception
            return null;
        } catch (\Exception $e) {
            // todo: handle the exception
            return null;
        }
        if($session) {
            $request = new FacebookRequest($session, 'GET', '/me');
            $response = $request->execute();
            /** @var GraphUser $graphUser */
            $graphUser = $response->getGraphObject(GraphUser::className());
            $email = $graphUser->getProperty('email');

            $user = new User;
            if(null != $email) {
                $user->setEmail((string) $email);
            }
            $user->setFirstName($graphUser->getFirstName());
            $user->setLastName($graphUser->getLastName());
            $user->setName($graphUser->getName());
            $user->setFacebookId($graphUser->getId());
            return $user;
        }
        return null;
    }
}