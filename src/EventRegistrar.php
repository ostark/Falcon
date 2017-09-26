<?php namespace ostark\falcon;

use craft\base\Element;
use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\events\ElementEvent;
use craft\events\ElementStructureEvent;
use craft\events\PopulateElementEvent;
use craft\events\RegisterCacheOptionsEvent;
use craft\events\SectionEvent;
use craft\events\TemplateEvent;
use craft\helpers\Db;
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

        Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, function($event) {
            static::handleUpdateEvent($event);
        });
        Event::on(Structures::class, Structures::EVENT_AFTER_MOVE_ELEMENT, function($event) {
            static::handleUpdateEvent($event);
        });
        Event::on(Sections::class, Sections::EVENT_AFTER_SAVE_SECTION, function($event) {
            static::handleUpdateEvent($event);
        });
        Event::on(Element::class, Element::EVENT_AFTER_MOVE_IN_STRUCTURE, function($event) {
            static::handleUpdateEvent($event);
        });
    }

    public static function registerFrontendEvents()
    {
        // No need to continue when in cli mode
        if (\Craft::$app instanceof \craft\console\Application) {
            return false;
        }


        // HTTP request object
        $request = \Craft::$app->getRequest();

        // Don't cache CP, LivePreview, Non-GET requests
        if ($request->getIsCpRequest() ||
            $request->getIsLivePreview() ||
            !$request->getIsGet()
        ) {
            return false;
        }

        // Collect tags
        Event::on(ElementQuery::class, ElementQuery::EVENT_AFTER_POPULATE_ELEMENT, function (PopulateElementEvent $event) {

            // Don't collect MatrixBlock and User elements for now
            if (in_array(get_class($event->element), ['craft\elements\User', 'craft\elements\MatrixBlock'])) {
                return;
            }

            // Add to collection
            Plugin::getInstance()->getTagCollection()->addTagsFromElement($event->row);

        });

        // Add the tags to the response header
        Event::on(View::class, View::EVENT_AFTER_RENDER_PAGE_TEMPLATE, function (TemplateEvent $event) {

            $plugin   = Plugin::getInstance();
            $response = \Craft::$app->getResponse();
            $tags     = $plugin->getTagCollection()->getAll();
            $settings = $plugin->getSettings();
            $headers  = $response->getHeaders();

            // Make existing cache-control headers accessible
            $response->setCacheControlDirectiveFromString($headers->get('cache-control'));

            // Don't cache if private | no-cache set already
            if ($response->hasCacheControlDirective('private') || $response->hasCacheControlDirective('no-cache')) {
                return;
            }

            // MaxAge or defaultMaxAge?
            $maxAge = $response->getMaxAge() ?? $settings->defaultMaxAge;

            // Set Headers
            $response->setTagHeader($settings->getHeaderName(), $tags, $settings->getHeaderTagDelimiter());
            $response->setSharedMaxAge($maxAge);

            $plugin->trigger($plugin::EVENT_AFTER_SET_TAG_HEADER, new CacheResponseEvent([
                    'tags'       => $tags,
                    'maxAge'     => $maxAge,
                    'requestUrl' => \Craft::$app->getRequest()->getUrl(),
                    'output'     => $event->output,
                    'headers'    => $response->getHeaders()->toArray()
                ]
            ));
        });

        Event::on(Plugin::class, Plugin::EVENT_AFTER_SET_TAG_HEADER, function (CacheResponseEvent $event) {


            $cacheItemId = (new Query())
                ->select(['id'])
                ->from([Plugin::TABLE_CACHE_ITEMS])
                ->where(['url' => $event->requestUrl])
                ->scalar();

            // Remove existing cache
            if ($cacheItemId) {

                \Craft::$app
                    ->getDb()
                    ->createCommand()
                    ->delete(Plugin::TABLE_CACHE_ITEMS, ['id' => $cacheItemId])
                    ->execute();

                \Craft::$app
                    ->getDb()
                    ->createCommand()
                    ->delete(Plugin::TABLE_CACHE_TAGS, ['cacheItemId' => $cacheItemId])
                    ->execute();
            }

            // Insert item
            \Craft::$app->getDb()->createCommand()
                ->insert(
                    Plugin::TABLE_CACHE_ITEMS,
                    [
                        'url'         => $event->requestUrl,
                        'body'        => $event->output,
                        'headers'     => json_encode($event->headers),
                        'maxAge'      => $event->maxAge,
                        'ttl'         => Db::prepareDateForDb(new \DateTime('@' . (time() + $event->maxAge))),
                        'siteId'      => \Craft::$app->getSites()->currentSite->id,
                        'dateCreated' => Db::prepareDateForDb(new \DateTime())
                    ],
                    false)
                ->execute();

            $cacheItemId = \Craft::$app->getDb()->getLastInsertID(Plugin::TABLE_CACHE_ITEMS);
            $values      = [];

            foreach ($event->tags as $tag) {
                $values[] = [$cacheItemId, $tag];
            }

            // Insert tags
            \Craft::$app->getDb()->createCommand()
                ->batchInsert(Plugin::TABLE_CACHE_TAGS, ['cacheItemId', 'tag'], $values, false)
                ->execute();



        });


    }

    public static function registerDashboardEvents()
    {
        // Register cache purge checkbox
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
    protected static function handleUpdateEvent(Event $event)
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
        \Craft::$app->getQueue()->push(new Job(function () use ($keys, $purger) {
            $purger->purgeByKeys($keys);
        }));

    }


}
