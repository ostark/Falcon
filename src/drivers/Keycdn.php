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
        $this->sendPurgeRequest('purgetag', [
                'tags' => $keys
            ]
        );

        return true;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function purgeByUrl(string $url)
    {
        $url = $this->domain . $url;
        $this->sendPurgeRequest('purgeurl', [
                'urls' => [$url]
            ]
        );

        return true;
    }


    /**
     * @return bool
     */
    public function purgeAll()
    {
        $this->sendPurgeRequest('purge', [], 'GET');

        return true;

    }

    /**
     * @param string $type
     * @param array  $params
     * @param string $method HTTP verb
     *
     * @return bool
     */
    protected function sendPurgeRequest(string $type, array $params = [], $method = 'DELETE')
    {

        $apiKey  = base64_encode("{$this->apiKey}");
        $headers = ['Content-Type' => 'application/json', 'Authorization' => "Basic {$apiKey}"];
        $client  = new Client([
            'base_uri' => self::API_ENDPOINT,
            'headers'  => $headers
        ]);

        try {

            $options  = (count($params)) ? ['form_params' => $params] : [];
            $response = $client->request($method, "zones/{$type}/{$this->zoneId}.json", $options);

            if (!in_array($response->getStatusCode(), [204, 200])) {
                error_log($response->getStatusCode() . ' > ' . $response->getBody());

                return false;
            }

        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

    }
}
