<?php

namespace ostark\falcon\migrations;

use Craft;
use craft\db\Migration;
use ostark\falcon\Plugin;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(Plugin::TABLE_CACHE_ITEMS, [
            'id'          => $this->primaryKey()->unsigned(),
            'url'         => $this->string(255)->notNull(),
            'content'     => $this->mediumText()->defaultValue(null),
            'maxAge'      => $this->bigInteger()->notNull(),
            'siteId'      => $this->integer(),
            'dateCreated' => $this->dateTime()->notNull()
        ]);

        $this->createTable(Plugin::TABLE_CACHE_TAGS, [
            'id'          => $this->primaryKey()->unsigned(),
            'cacheItemId' => $this->integer()->unsigned(),
            'tag'         => $this->string(64)->notNull()
        ]);

        $this->createIndex(null, PLugin::TABLE_CACHE_ITEMS, 'url', true);
        $this->createIndex(null, PLugin::TABLE_CACHE_ITEMS, 'dateCreated');
        $this->createIndex(null, PLugin::TABLE_CACHE_TAGS, 'tag');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists(Plugin::TABLE_CACHE_ITEMS);
        $this->dropTableIfExists(Plugin::TABLE_CACHE_TAGS);
    }
}
