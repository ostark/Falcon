<?php namespace ostark\falcon\drivers;


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
     */
    public function purgeByKeys(array $keys)
    {
        $this->sendPurgeRequest('purgetag', [
                'tags' => $keys
            ]
        );
    }

    /**
     * @param string $url
     */
            public function purgeByUrl(string $url)
    {
        $url = $this->domain . $url;
        $this->sendPurgeRequest('purgeurl', [
                'urls' => [$url]
            ]
        );
    }


    public function purgeAll()
    {
        // TODO: Implement purgeAll() method.
    }

    protected function sendPurgeRequest(string $type, array $params = [])
    {

        $apiKey  = base64_encode("{$this->apiKey}:");
        $headers = ['Content-Type' => 'application/json', 'Authorization' => "Basic {$apiKey}"];
        $client  = new Client([
            'base_uri' => self::API_ENDPOINT,
            'headers'  => $headers
        ]);

        try {

            $response = $client->request('DELETE', "zones/{$type}/{$this->zoneId}.json", [
                'form_params' => compact($params)
            ]);
            if (!in_array($response->getStatusCode(), [204, 200])) {
                error_log($response->getStatusCode() . ' > ' . $response->getBody());

                return false;
            }

        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

    }
}
