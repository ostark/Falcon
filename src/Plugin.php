<?php namespace ostark\falcon;


use Craft;
use craft\base\Plugin as BasePlugin;
use ostark\falcon\behaviors\CacheControlBehavior;
use ostark\falcon\behaviors\TagHeaderBehavior;
use ostark\falcon\drivers\CachePurgeInterface;
use ostark\falcon\models\Settings;
use yii\queue\closure\Behavior;

/**
 * @method    Settings getSettings()
 */
class Plugin extends BasePlugin
{
    // Event names
    const EVENT_AFTER_SET_TAG_HEADER = 'falcon_after_set_tag_header';
    const EVENT_AFTER_PURGE = 'falcon_after_purge';

    // Tag prefixes
    const TAG_PREFIX_ELEMENT = 'el';
    const TAG_PREFIX_SECTION = 'se';
    const TAG_PREFIX_STRUCTURE = 'st';

    // Mapping element properties <> tag prefixes
    const ELEMENT_PROPERTY_MAP = [
        'id'          => self::TAG_PREFIX_ELEMENT,
        'sectionId'   => self::TAG_PREFIX_SECTION,
        'structureId' => self::TAG_PREFIX_STRUCTURE
    ];

    // DB
    const TABLE_CACHE_ITEMS = '{{%falcon_cacheitems}}';
    const TABLE_CACHE_TAGS = '{{%falcon_cachetags}}';

    public $schemaVersion = '1.0.0';


    /**
     * Initialize Plugin
     */
    public function init()
    {
        parent::init();

        // Config pre-check
        if (!isset($this->getSettings()->driver)) {
            return false;
        }

        // Register plugin components
        $this->setComponents([
            'purger'        => PurgerFactory::create($this->getSettings()->toArray()),
            'tagCollection' => TagCollection::class
        ]);

        // Register event handlers
        EventRegistrar::registerFrontendEvents();
        EventRegistrar::registerUpdateEvents();
        EventRegistrar::registerDashboardEvents();

        // Attach Behaviors
        \Craft::$app->getResponse()->attachBehavior('cache-control', CacheControlBehavior::class);
        \Craft::$app->getResponse()->attachBehavior('tag-header', TagHeaderBehavior::class);

    }


    // ServiceLocators
    // =========================================================================

    /**
     * @return \ostark\falcon\drivers\CachePurgeInterface
     */
    public function getPurger(): CachePurgeInterface
    {
        return $this->get('purger');
    }


    /**
     * @return \ostark\falcon\TagCollection
     */
    public function getTagCollection(): TagCollection
    {
        return $this->get('tagCollection');
    }


    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }


    /**
     * Is called after the plugin is installed.
     * Copies example config to project's config folder
     */
    protected function afterInstall()
    {
        $configSourceFile = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
        $configTargetFile = \Craft::$app->getConfig()->configDir . DIRECTORY_SEPARATOR . $this->handle . '.php';

        if (!file_exists($configTargetFile)) {
            copy($configSourceFile, $configTargetFile);
        }
    }


}
