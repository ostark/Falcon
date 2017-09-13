<?php namespace joshangell\falcon\drivers;

/**
 * Created by PhpStorm.
 * User: os
 * Date: 13.09.17
 * Time: 12:55
 */
interface CacheInterface
{
    /**
     * @param array $keys
     *
     * @return mixed
     */
    public function purgeByKeys(array $keys);

    /**
     * @param string $url
     *
     * @return mixed
     */
    public function purgeByUrl(string $url);

}
