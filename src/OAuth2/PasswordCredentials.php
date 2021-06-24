<?php


namespace Aleahy\SaasuConnect\OAuth2;


use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use kamermans\OAuth2\Signer\ClientCredentials\SignerInterface;
use kamermans\OAuth2\Utils\Collection;
use GuzzleHttp\Psr7\Request;
use kamermans\OAuth2\Utils\Helper;

class PasswordCredentials implements \kamermans\OAuth2\GrantType\GrantTypeInterface
{

    /**
     * The token endpoint client.
     *
     * @var ClientInterface
     */
    private $client;

    /**
     * Configuration settings.
     *
     * @var Collection
     */
    private $config;

    public function __construct(ClientInterface $client, array $config)
    {
        $this->client = $client;
        $this->config = Collection::fromConfig(
            $config,
            // Default
            [
                'scope' => 'full',
            ],
            // Required
            [
                'username',
                'password',
            ]
        );
    }
    /**
     * @inheritDoc
     */
    public function getRawData(SignerInterface $clientCredentialsSigner, $refreshToken = null)
    {
        $response = $this->client->post('/authorisation/token', $this->getPostBody());

        return json_decode($response->getBody(), true);
    }


    protected function getPostBody()
    {
        return [
            'json' => [
                'grant_type' => 'password',
                'username' => $this->config['username'],
                'password' => $this->config['password'],
                'scope' => 'full'
            ]
        ];
    }
}