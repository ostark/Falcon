<?php namespace ostark\falcon\drivers;

use GuzzleHttp\Client;


class Varnish extends AbstractPurger implements CachePurgeInterface
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

    public function purgeAll()
    {
        // TODO: Implement purgeAll() method.
    }

    protected function sendPurgeRequest(array $options = []){

        $client  = new Client([]);

        try {

        } catch (\Exception $e) {
            // $e->getMessage()
        }

    }
}
