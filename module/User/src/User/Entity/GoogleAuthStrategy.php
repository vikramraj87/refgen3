<?php
namespace User\Entity;

require_once 'Google/Client.php';

use Zend\Http\Client as HttpClient,
    Zend\Http\Request,
    Zend\Http\Response,
    Zend\Http\Header\Authorization,
    Zend\Http\Client\Exception\ExceptionInterface as ClientException;
use Google_Client;


class GoogleAuthStrategy extends AbstractAuthStrategy
{
    protected $client;

    /**
     * @return \User\Entity\User|null
     */
    public function getUser()
    {
        $code = isset($_GET['code']) ? $_GET['code'] : '';
        if(empty($code)) {
            return null;
        }

        $this->getClient()->authenticate($code);

        $jsonData    = $this->getClient()->getAccessToken();
        $data        = json_decode($jsonData, true);
        $accessToken = $data['access_token'];

        $request     = new Request();
        $request->setUri('https://www.googleapis.com/oauth2/v2/userinfo');
        $request->getHeaders()->addHeader(new Authorization('Bearer ' . $accessToken));
        $request->setMethod(Request::METHOD_GET);

        $client = new HttpClient();
        $client->setRequest($request);

        try {
            /** @var Response $response */
            $response = $client->send();
        } catch(ClientException $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }

        if(!$response->isSuccess()) {
            return null;
        }

        $userData = json_decode($response->getBody(), true);
        $user = new User();
        $user->setEmail($userData['email']);
        $user->setFirstName($userData['given_name']);
        $user->setLastName($userData['family_name']);
        $user->setName($userData['name']);

        return $user;
    }

    private function getClient()
    {
        if(null === $this->client) {
            $client = new Google_Client();

            // Routine configurations
            $client->setClientId($this->clientId);
            $client->setClientSecret($this->clientSecret);
            $client->setRedirectUri($this->redirectUrl);
            $client->setScopes($this->scopes);

            // Specific configurations
            $client->setAccessType('online');
            $client->setApprovalPrompt('auto');

            $this->client = $client;
        }
        return $this->client;
    }

} 