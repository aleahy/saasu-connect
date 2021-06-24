<?php


namespace Aleahy\SaasuConnect;

use Aleahy\SaasuConnect\OAuth2\PasswordCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use kamermans\OAuth2\OAuth2Middleware;
use Spatie\GuzzleRateLimiterMiddleware\RateLimiterMiddleware;

class SaasuAPI
{
    private string $fileID;
    private Client $client;

    public static function createClient(string $username,
                                        string $password,
                                        $baseURI = 'https://api.saasu.com'): Client
    {
        $reauth_client = new Client([
            'base_uri' => 'https://api.saasu.com'
        ]);
        $reauth_config = [
            'client_id' => '',
            'username' => $username,
            'password' => $password,
            'scope' => 'full'
        ];
        $grant_type = new PasswordCredentials($reauth_client, $reauth_config);
        $stack = HandlerStack::create();
        $stack->push(new OAuth2Middleware($grant_type));
        $stack->push(RateLimiterMiddleware::perSecond(1));
        return new Client([
            'base_uri' => $baseURI,
            'handler' => $stack,
            'auth' => 'oauth',
        ]);
    }

    public function __construct(Client $client,
                                string $fileID)
    {
        $this->client = $client;
        $this->fileID = $fileID;

    }



    public function findEntity(string $entityName, array $searchParameters)
    {
        $endpoint = $entityName::SEARCH_ENDPOINT;
        $query = array_merge(['FileId' => $this->fileID], $searchParameters);
        $uri = $entityName::SEARCH_ENDPOINT . '?' . http_build_query($query);
        $response = $this->client->request('GET', $uri);

        $json = $response->getBody()->getContents();
        return json_decode($json);
    }

    public function insertEntity(string $entityName, array $attributes)
    {
        $uri = $this->getUriForPost($entityName);
        $response = $this->client->request('POST', $uri, [
            'json' => $attributes
        ]);
        $json = $response->getBody()->getContents();
        return json_decode($json);
    }

    public function getEntity(string $entityName, $id)
    {
        $uri = $this->getUriForGetById($entityName, $id);
        $response = $this->client->request('GET', $uri);
        $json = $response->getBody()->getContents();
        return json_decode($json);
    }

    public function getUriForPost(string $entityName)
    {
        return $entityName::SINGLE_ENDPOINT . '?' . 'FileId=' . $this->fileID;
    }

    public function getUriForGetById(string $entityName, $id)
    {
        return $entityName::SINGLE_ENDPOINT . '/' . $id . '?' . 'FileId=' . $this->fileID;
    }

    public function getUriForSearch(string $entityName, array $searchParameters)
    {
        $query = array_merge(['FileId' => $this->fileID], $searchParameters);
        return $entityName::SEARCH_ENDPOINT . '?' . http_build_query($query);
    }

}