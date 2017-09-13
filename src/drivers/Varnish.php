<?php namespace joshangell\falcon\drivers;

use GuzzleHttp\Client;


class Varnish extends AbstractDriver implements CacheInterface
{

    public function purgeByKeys(array $keys)
    {
        $this->sendPurgeRequest([
                'headers' => [$this->headerName => implode(" " . $keys)]
            ]
        );
    }

    public function purgeByUrl(string $url)
    {
        $this->sendPurgeRequest([
                'url' => $url
            ]
        );
    }

    protected function sendPurgeRequest(array $options = []){

        $client  = new Client([]);

        try {

        } catch (\Exception $e) {
            // $e->getMessage()
        }

    }
}
