<?php namespace joshangell\falcon\services;

use Psr\Log\InvalidArgumentException;
use yii\base\Component;

class PurgerFactory extends Component
{
    const DRIVERS_NAMESPACE = 'joshangell\falcon\drivers';

    /**
     * @param array $config
     *
     * @return \joshangell\falcon\drivers\CachePurgeInterface
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


        return \Craft::createObject($driverClass,[$driverConfig]);

    }
}
