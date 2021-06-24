<?php


namespace Aleahy\SaasuConnect;


use Aleahy\SaasuConnect\Entities\Company;
use Aleahy\SaasuConnect\OAuth2\PasswordCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use kamermans\OAuth2\OAuth2Middleware;
use Spatie\GuzzleRateLimiterMiddleware\RateLimiterMiddleware;

class SaasuAPI
{
    protected string $fileID;
    protected Client $client;
    protected string $baseURI;
    protected string $username;
    protected string $password;

    public function __construct(string $fileID,
                                string $username,
                                string $password,
                                string $baseURI = 'https://api.saasu.com')
    {
        $this->fileID = $fileID;
        $this->baseURI = $baseURI;
        $this->username = $username;
        $this->password = $password;
        $reauth_client = new Client([
            'base_uri' => 'https://api.saasu.com'
        ]);
        $reauth_config = [
            'client_id' => '',
            'username' => $this->username,
            'password' => $this->password,
            'scope' => 'full'
        ];
        $grant_type = new PasswordCredentials($reauth_client, $reauth_config);
        $stack = HandlerStack::create();
        $stack->push(new OAuth2Middleware($grant_type));
        $stack->push(RateLimiterMiddleware::perSecond(1));
        $this->client = new Client([
            'base_uri' => $this->baseURI,
            'handler' => $stack,
            'auth' => 'oauth',
        ]);
    }



    public function findEntity(string $entityName, array $searchParameters)
    {
        $request = $entityName::getSearchRequest($searchParameters, $this->fileID);

        $response = $this->client->send($request);

        $json = $response->getBody()->getContents();
        return json_decode($json);
    }

    public function insertEntity(string $entityName, array $attributes)
    {
        $uri = $entityName::SINGLE_ENDPOINT . '?' . 'FileId=' . $this->fileID;
        $response = $this->client->request('POST', $uri, [
            'json' => $attributes
        ]);
        $json = $response->getBody()->getContents();
        return json_decode($json);
    }

    public function getEntity(string $entityName, $id)
    {
        $uri = $entityName::SINGLE_ENDPOINT . '/' . $id . '?' . 'FileId=' . $this->fileID;
        $response = $this->client->request('GET', $uri);
        $json = $response->getBody()->getContents();
        return json_decode($json);
    }

}