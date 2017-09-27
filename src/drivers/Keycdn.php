<?php

namespace ostark\falcon\drivers;


use GuzzleHttp\Client;

class Keycdn extends AbstractPurger implements CachePurgeInterface
{

    /**
     * KeyCDN API endpoint
     */
    const API_ENDPOINT = 'https://api.keycdn.com/';

    public $apiKey;

    public $zoneId;

    public $domain;


    /**
     * @param array $keys
     *
     * @return bool
     */
    public function purgeByKeys(array $keys)
    {
        return $this->sendRequest('purgetag', [
                'tags' => $keys
            ]
        );

    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function purgeByUrl(string $url)
    {
        return $this->sendRequest('purgeurl', [
                'urls' => [$this->domain . $url]
            ]
        );
    }


    /**
     * @return bool
     */
    public function purgeAll()
    {
        return $this->sendRequest('purge', [], 'GET');
    }

    /**
     * @param string $type
     * @param array  $params
     * @param string $method HTTP verb
     *
     * @return bool
     */
    protected function sendRequest(string $type, array $params = [], $method = 'DELETE')
    {
        $token  = base64_encode("{$this->apiKey}:");
        $client = new Client([
            'base_uri' => self::API_ENDPOINT,
            'headers'  => [
                'Content-Type'  => 'application/json',
                'Authorization' => "Basic {$token}"
            ]
        ]);

        $url     = "zones/{$type}/{$this->zoneId}.json";
        $options = (count($params)) ? ['json' => $params] : [];

        $response = $client->request($method, $url, $options);

        return (in_array($response->getStatusCode(), [204, 200]))
            ? true
            : false;

    }
}
