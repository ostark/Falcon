<?php namespace ostark\falcon\drivers;



class LocalProxy extends AbstractPurger implements CachePurgeInterface
{

    /**
     * @param array $keys
     */
    public function purgeByKeys(array $keys)
    {
       // TODO: find urls by key + .. $this->purgeByUrl()
    }

    /**
     * @param string $url
     */
    public function purgeByUrl(string $url)
    {
        // TODO: Implement purgeByUrl() method.

    }


    public function purgeAll()
    {
        // TODO: Implement purgeAll() method.
    }

}
