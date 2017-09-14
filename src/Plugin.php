<?php namespace joshangell\falcon;


use Craft;
use craft\base\Plugin as BasePlugin;
use craft\web\View;
use craft\base\Element;
use craft\services\Elements;
use craft\services\Sections;
use craft\services\Structures;
use joshangell\falcon\services\PurgerFactory;
use joshangell\falcon\models\Settings;
use yii\base\Event;

/**
 * @method    Settings getSettings()
 */
class Plugin extends BasePlugin
{
    // Event names
    const EVENT_AFTER_TAG_HEADER = 'falcon_after_tag_header';
    const EVENT_AFTER_PURGE = 'falcon_after_purge';

    // Tag prefixes
    const TAG_PREFIX_ELEMENT = 'el:';
    const TAG_PREFIX_SECTION = 'se:';
    const TAG_PREFIX_STRUCTURE = 'st:';

    /**
     * Initialize Plugin
     */
    public function init()
    {
        parent::init();

        $this->createPurger();
        $this->addTagHeaderAfterRender();
        $this->listenToUpdateEvents();

    }

    // Protected Methods
    // =========================================================================


    protected function createPurger()
    {
        $config = $this->getSettings()->toArray();
        $this->set('purger', PurgerFactory::create($config));
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

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }


}
