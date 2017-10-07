<?php namespace ostark\falcon\drivers;

/**
 * Created by PhpStorm.
 * User: os
 * Date: 13.09.17
 * Time: 12:55
 */
interface CachePurgeInterface
{
    /**
     * @param array $keys
     *
     * @return bool
     */
    public function purgeByKeys(array $keys);

    /**
     * @param string $url
     *
     * @return bool
     */
    public function purgeByUrl(string $url);


    /**
     * @return bool
     */
    public function purgeAll();

}
