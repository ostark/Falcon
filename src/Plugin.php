<?php namespace joshangell\falcon;


use Craft;
use craft\base\Plugin as BasePlugin;
use joshangell\falcon\drivers\CachePurgeInterface;
use joshangell\falcon\models\Settings;

/**
 * @method    Settings getSettings()
 */
class Plugin extends BasePlugin
{
    // Event names
    const EVENT_AFTER_SET_TAG_HEADER = 'falcon_after_set_tag_header';
    const EVENT_AFTER_PURGE = 'falcon_after_purge';

    // Tag prefixes
    const TAG_PREFIX_ELEMENT = 'el:';
    const TAG_PREFIX_SECTION = 'se:';
    const TAG_PREFIX_STRUCTURE = 'st:';

    // Mapping element properties <> tag prefixes
    const ELEMENT_PROPERTY_MAP = [
        'id'          => self::TAG_PREFIX_ELEMENT,
        'sectionId'   => self::TAG_PREFIX_SECTION,
        'structureId' => self::TAG_PREFIX_STRUCTURE
    ];

    /**
     * Initialize Plugin
     */
    public function init()
    {
        parent::init();

        // Register plugin components
        $this->setComponents([
            'purger'        => PurgerFactory::create($this->getSettings()->toArray()),
            'tagCollection' => TagCollection::class
        ]);

        // Register event handlers
        EventRegistrar::registerFrontendEvents();
        EventRegistrar::registerUpdateEvents();


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
     * @return \joshangell\falcon\drivers\CachePurgeInterface
     */
    public function getPurger(): CachePurgeInterface
    {
        return $this->get('purger');
    }

    /**
     * @return \joshangell\falcon\TagCollection
     */
    public function getTagCollection(): TagCollection
    {
        return $this->get('tagCollection');
    }


}
