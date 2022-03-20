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

    /**
     * Create a guzzle client with the required parameters to use the saasu api.
     *
     * @param string $username
     * @param string $password
     * @param string $baseURI
     * @return Client
     */
    public static function createClient(string $username,
                                        string $password,
                                        string $baseURI = 'https://api.saasu.com'): Client
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

    /**
     * Make a get request to find occurrences of the entity with the provided search attributes.
     *
     * @param string $entityName
     * @param array $searchParameters
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function findEntities(string $entityName, array $searchParameters)
    {
        $endpoint = $entityName::SEARCH_ENDPOINT;

        $query = array_merge(['FileId' => $this->fileID], $searchParameters);

        $uri = $entityName::SEARCH_ENDPOINT . '?' . http_build_query($query);

        $response = $this->client->request('GET', $uri);

        $json = $response->getBody()->getContents();

        $data = json_decode($json, true);

        return $data[$endpoint];
    }

    /**
     * Retrieve all specified entities
     *
     * @param string $entityName
     * @param array $searchParams
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllEntities(string $entityName, array $searchParams = [])
    {
        $entities = [];
        $params = ['PageSize' => 100] + $searchParams;
        $page = 1;

        $resp = $this->findEntities($entityName, $params);

        while(count($resp) > 0) {
            $entities = array_merge($entities, $resp);

            $page++;
            $params = ['PageSize' => 100, 'Page' => $page] + $searchParams;

            $resp = $this->findEntities($entityName, $params);
        }

        return $entities;
    }

    /**
     * Make a post request to insert an entity with the provided attributes.
     *
     * @param string $entityName
     * @param array $attributes
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function insertEntity(string $entityName, array $attributes)
    {
        $uri = $this->getUriForPost($entityName);

        $response = $this->client->request('POST', $uri, [
            'json' => $attributes
        ]);

        $json = $response->getBody()->getContents();

        return json_decode($json, true);
    }

    /**
     * Get the specific entity with the provided id
     *
     * @param string $entityName
     * @param int $id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEntity(string $entityName, int $id)
    {
        $uri = $this->getUriForGetById($entityName, $id);

        $response = $this->client->request('GET', $uri);

        $json = $response->getBody()->getContents();

        return json_decode($json, true);
    }

    /**
     * Update a specific entity with the provided attributes.
     * Note: LastUpdatedId must be supplied.
     *
     * @param string $entityName
     * @param int $id
     * @param array $attributes
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateEntity(string $entityName, int $id, array $attributes)
    {
        $uri = $this->getUriForGetById($entityName, $id);

        $response = $this->client->request('PUT', $uri, [
            'json' => $attributes
        ]);

        $json = $response->getBody()->getContents();

        return json_decode($json, true);
    }


    /**
     * Create the required uri to make a post request for the entity.
     *
     * @param string $entityName
     * @return string
     */
    public function getUriForPost(string $entityName): string
    {
        return $entityName::SINGLE_ENDPOINT . '?' . 'FileId=' . $this->fileID;
    }

    /**
     * Create the required uri to make a get by id request for an entity.
     *
     * @param string $entityName
     * @param int $id
     * @return string
     */
    public function getUriForGetById(string $entityName, int $id): string
    {
        return $entityName::SINGLE_ENDPOINT . '/' . $id . '?' . 'FileId=' . $this->fileID;
    }

    /**
     * Create a required uri to make a search request for an entity.
     *
     * @param string $entityName
     * @param array $searchParameters
     * @return string
     */
    public function getUriForSearch(string $entityName, array $searchParameters): string
    {
        $query = array_merge(['FileId' => $this->fileID], $searchParameters);
        return $entityName::SEARCH_ENDPOINT . '?' . http_build_query($query);
    }

}