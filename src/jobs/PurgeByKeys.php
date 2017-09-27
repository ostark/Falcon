<?php namespace ostark\falcon\jobs;

use Craft;
use craft\queue\BaseJob;
use ostark\falcon\Plugin;

/**
 * PurgeCacheByTags job
 *
 */
class PurgeByKeys extends BaseJob
{
    // Properties
    // =========================================================================

    /**
     * @var array keys
     */
    public $keys = [];

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        // Get registered purger
        $purger = Plugin::getInstance()->getPurger();
        $purger->purgeByKeys($this->keys);

    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        $keys = implode(', ', $this->keys);

        return Craft::t('falcon', 'Purge Keys: {keys}', ['keys' => $keys]);
    }
}
