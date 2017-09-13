<?php namespace joshangell\falcon;

use craft\base\Element;
use craft\services\Elements;
use craft\services\Sections;
use craft\services\Structures;
use joshangell\falcon\services\CacheFactory;

use Craft;
use craft\base\Plugin as BasePlugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\View;

use joshangell\falcon\models\Settings;
use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Oliver Stark
 * @package   Falcon
 * @since     0.1.0
 *
 * @property  \joshangell\falcon\services\CacheFactory $edgeCache
 * @property  Settings                                 $settings
 * @method    Settings getSettings()
 */
class Plugin extends BasePlugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Falcon::$plugin
     *
     * @var Falcon
     */
    public static $plugin;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Falcon::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->createPurger();
        $this->addTagHeaderAfterRender();
        $this->listenToUpdateEvents();

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

    protected function createPurger()
    {
        $config = $this->getSettings()->toArray();
        $this->set('purger', CacheFactory::create($config));
    }

    protected function addTagHeaderAfterRender()
    {
        Event::on(View::class, View::EVENT_AFTER_RENDER_PAGE_TEMPLATE, function ($event) {

            /*
            $cacheKeys = ['id' => [], 'sectionId' => [], 'structureId' => []];
            // collect
            foreach ($elements as $el) {
                foreach (array_keys($cacheKeys) as $key) {
                    if (isset($el[$key])) {
                        $cacheKeys[$key][] = $el[$key];
                    }
                }
            }
            foreach ($cacheKeys as $key => $values) {
                $cacheKeys[$key] = array_unique($values);
            }
            // add headers
            foreach ($cacheKeys as $key => $values) {
                foreach ($values as $value) {
                    \Craft::$app->getResponse()->getHeaders()->add('X-CACHE-TAG', "$key:$value");
                }
            }
            */
            \Craft::$app->getResponse()->getHeaders()->add($this->getSettings()->headerName, "demo:foo");
        });

    }

    protected function listenToUpdateEvents()
    {

        Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, function ($event) {
            if (!$event->isNew) {
                error_log("EVENT_AFTER_SAVE_ELEMENT - id: " . $event->element->id . " .. type: " . get_class($event->element));
            }
        });
        Event::on(Structures::class, Structures::EVENT_AFTER_MOVE_ELEMENT, function ($event) {
            error_log("EVENT_AFTER_MOVE_ELEMENT - structureId: " . $event->structureId);
        });
        Event::on(Sections::class, Sections::EVENT_AFTER_SAVE_SECTION, function ($event) {
            error_log("EVENT_AFTER_SAVE_SECTION - sectionId: " . $event->section->id);
        });
        Event::on(Element::class, Element::EVENT_AFTER_MOVE_IN_STRUCTURE, function ($event) {
            error_log("EVENT_AFTER_MOVE_IN_STRUCTURE  - structureId: " . $event->structureId);
        });

    }


}
