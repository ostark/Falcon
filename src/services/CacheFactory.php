<?php namespace joshangell\falcon\services;

use joshangell\falcon\drivers\CacheInterface;
use joshangell\falcon\drivers\Varnish;
use Psr\Log\InvalidArgumentException;
use yii\base\Component;

class CacheFactory extends Component
{

    const DRIVERS_NAMESPACE = 'joshangell\falcon\drivers';

    /**
     * @param array $config
     *
     * @return \joshangell\falcon\drivers\CacheInterface
     */
    public static function create(array $config = [])
    {
        if (!isset($config['driver'])) {
            throw new InvalidArgumentException("'driver' in config missing");
        }
        if (!isset($config['drivers'][$config['driver']])) {
            throw new InvalidArgumentException("driver '{$config['driver']}' is not configured");
        }
        if (!isset($config['drivers'][$config['driver']]['headerName'])) {
            throw new InvalidArgumentException("'headerName' is not configured");
        }

        $driverConfig = $config['drivers'][$config['driver']];
        $driverClass = $driverConfig['class'] ?? self::DRIVERS_NAMESPACE . '\\' . ucfirst($config['driver']);


        \Craft::createObject($driverClass,[$driverConfig]);

    }
}
