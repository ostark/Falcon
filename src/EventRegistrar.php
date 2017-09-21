<?php namespace ostark\falcon;

use craft\base\Element;
use craft\elements\db\ElementQuery;
use craft\events\ElementEvent;
use craft\events\ElementStructureEvent;
use craft\events\PopulateElementEvent;
use craft\events\RegisterCacheOptionsEvent;
use craft\events\SectionEvent;
use craft\events\TemplateEvent;
use craft\services\Elements;
use craft\services\Sections;
use craft\services\Structures;
use craft\utilities\ClearCaches;
use craft\web\Session;
use craft\web\View;
use ostark\falcon\events\CacheResponseEvent;
use yii\base\Event;


class EventRegistrar
{

    public static function registerUpdateEvents()
    {
        Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, [EventRegistrar::class, 'handleUpdateEvent']);
        Event::on(Structures::class, Structures::EVENT_AFTER_MOVE_ELEMENT, [EventRegistrar::class, 'handleUpdateEvent']);
        Event::on(Sections::class, Sections::EVENT_AFTER_SAVE_SECTION, [EventRegistrar::class, 'handleUpdateEvent']);
        Event::on(Element::class, Element::EVENT_AFTER_MOVE_IN_STRUCTURE, [EventRegistrar::class, 'handleUpdateEvent']);
    }

    public static function registerFrontendEvents()
    {
        // Don't cache CP or LivePreview requests
        if (\Craft::$app->getRequest()->getIsCpRequest() ||
            \Craft::$app->getRequest()->getIsLivePreview()
        ) {
            return false;
        }

        // Collect tags
        Event::on(ElementQuery::class, ElementQuery::EVENT_AFTER_POPULATE_ELEMENT, function (PopulateElementEvent $event) {

            // Don't collect MatrixBlock and User elements for now
            if (in_array(get_class($event->element), ['craft\elements\User', 'craft\elements\MatrixBlock'])) {
                return;
            }
            // Collect elements only for frontend GET requests
            $request = \Craft::$app->getRequest();
            if ($request->getIsGet() && !$request->getIsCpRequest()) {
                Plugin::getInstance()->getTagCollection()->addTagsFromElement($event->row);
            }
        });

        // Add the tags to the response header
        Event::on(View::class, View::EVENT_AFTER_RENDER_PAGE_TEMPLATE, function (TemplateEvent $event) {

            $plugin    = Plugin::getInstance();
            $tags      = $plugin->getTagCollection()->getAll();
            $response  = \Craft::$app->getResponse();
            $headers   = $response->getHeaders();
            $settings  = Plugin::getInstance()->getSettings();
            $delimiter = $settings->getHeaderTagDelimiter();

            // Make existing cache-control headers accessible
            $response->setCacheControlDirectiveFromString($headers->get('cache-control'));

            // Don't cache
            if ($response->hasCacheControlDirective('private') || $response->hasCacheControlDirective('no-cache')) {
                return;
            }


            $maxAge = $response->getMaxAge() ?? $settings->defaultMaxAge;

            // Set Headers
            $response->setTagHeader($settings->getHeaderName(), $tags, $delimiter);
            $response->setSharedMaxAge($maxAge);

            $plugin->trigger($plugin::EVENT_AFTER_SET_TAG_HEADER, new CacheResponseEvent([
                    'tags'       => $tags,
                    'maxAge'     => $maxAge ?? $maxAge,
                    'requestUrl' => \Craft::$app->getRequest()->getUrl()
                ]
            ));

        });
    }

    public static function registerDashboardEvents()
    {
        Event::on(
            ClearCaches::class,
            ClearCaches::EVENT_REGISTER_CACHE_OPTIONS,
            function (RegisterCacheOptionsEvent $event) {
                $event->options[] = [
                    'key'    => 'falcon-purge-all',
                    'label'  => \Craft::t('falcon', 'Cache Proxy (Falcon Plugin)'),
                    'action' => function () {
                        Plugin::getInstance()->getPurger()->purgeAll();
                    },
                ];
            }
        );
    }

    /**
     * @param \yii\base\Event $event
     */
    protected function handleUpdateEvent(Event $event)
    {

        if ($event instanceof ElementEvent) {
            $keys = [Plugin::TAG_PREFIX_ELEMENT . $event->element->getId()];
        }
        if ($event instanceof SectionEvent) {
            $keys = [Plugin::TAG_PREFIX_SECTION . $event->section->id];
        }
        if ($event instanceof ElementStructureEvent) {
            $keys = [Plugin::TAG_PREFIX_STRUCTURE . $event->structureId];
        }

        // Get registered purger
        $purger = Plugin::getInstance()->getPurger();

        // Push to queue
        Craft::$app->getQueue()->push(new Job(function () use ($keys, $purger) {
            $purger->purgeByKeys($keys);
        }));

    }


}
