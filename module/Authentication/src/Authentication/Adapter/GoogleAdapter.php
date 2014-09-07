<?php
namespace Authentication\Adapter;

use Google_Client,
    Google_Service_Plus;
use Zend\Authentication\Adapter\Exception\UnexpectedValueException,
    Zend\Authentication\Result;
use Zend\Http\Client,
    Zend\Http\Request,
    Zend\Http\Header\Authorization,
    Zend\Http\Response;

class GoogleAdapter extends AbstractAdapter
{
    protected $id = 'Google';

    /** @var Google_Client */
    private $client;

    public function setCode($code)
    {
        if(null === $code) {
            throw new UnexpectedValueException;
        }
        $this->client->authenticate($code);
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        $plus = new Google_Service_Plus($this->client);
        $profile = $plus->people->get('me');
        $emails = $profile->getEmails();
        $user = array(
            'socialId'   => $profile->getId(),
            'email'      => $emails[0]['value'],
            'firstName'  => $profile->getName()->givenName,
            'middleName' => $profile->getName()->middleName,
            'lastName'   => $profile->getName()->familyName,
            'name'       => $profile->getDisplayName(),
            'picture'    => array(
                'link' => $profile->getImage()->url
            )
        );

        return new Result(Result::SUCCESS, $this->identityFromData($user));
    }

    protected function init()
    {
        $client = new Google_Client();
        $client->setClientId($this->clientId);
        $client->setClientSecret($this->clientSecret);
        $client->setRedirectUri($this->redirectUrl);
        $client->setScopes($this->scopes);
        $client->setAccessType('online');

        $this->loginUrl = $client->createAuthUrl();

        $this->client = $client;
    }
} 