<?php
namespace Authentication\Adapter;

use Google_Client;
use Zend\Authentication\Adapter\Exception\UnexpectedValueException,
    Zend\Authentication\Result;
use Zend\Http\Client,
    Zend\Http\Request,
    Zend\Http\Header\Authorization,
    Zend\Http\Response;

class GoogleAdapter extends AbstractAdapter
{
    protected $id = 2;

    /** @var Google_Client */
    private $client;

    /** @var string|null */
    private $code;

    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        if(null === $this->code) {
            throw new UnexpectedValueException;
        }
        $client = $this->client;
        $tokenData = json_decode($client->authenticate($this->code), true);
        $token = $tokenData['access_token'];

        $request = new Request();
        $request->setUri('https://www.googleapis.com/oauth2/v2/userinfo');
        $request->getHeaders()->addHeader(new Authorization('Bearer ' . $token));

        $httpClient = new Client();
        $httpClient->setAdapter('Zend\Http\Client\Adapter\Curl');
        $httpClient->setMethod(Request::METHOD_GET);
        $httpClient->setRequest($request);

        /** @var Response $response */
        $response = $httpClient->send();
        $userData = json_decode($response->getBody(), true);
        $user = array(
            'socialId'   => $userData['id'],
            'email'      => $userData['email'],
            'firstName'  => $userData['given_name'],
            'middleName' => '',
            'lastName'   => $userData['family_name'],
            'name'       => $userData['name'],
            'picture'    => array(
                'link' => $userData['picture']
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