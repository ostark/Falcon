<?php namespace joshangell\falcon;

use craft\base\Element;
use craft\elements\db\ElementQuery;
use craft\events\ElementEvent;
use craft\events\ElementStructureEvent;
use craft\events\PopulateElementEvent;
use craft\events\SectionEvent;
use craft\events\TemplateEvent;
use craft\services\Elements;
use craft\services\Sections;
use craft\services\Structures;
use craft\web\View;
use joshangell\falcon\events\CacheResponseEvent;
use yii\base\Event;
use yii\helpers\ArrayHelper;


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

            $plugin        = Plugin::getInstance();
            $tags          = $plugin->getTagCollection()->getAll();
            $response      = \Craft::$app->getResponse();
            $headers       = \Craft::$app->getResponse()->getHeaders();

            $delimiter     = " ";
            $defaultMaxAge = 10000;

            if (count($tags) === 0) {
                return;
            }

            // Make existing cache-control headers accessible
            if ($cc = $headers->get('cache-control')) {
                foreach (explode(', ', $cc) as $directive) {
                    $parts = explode('=', $directive);
                    $response->addCacheControlDirective($parts[0], $parts[1] ?? true);
                }
            }

            if ($response->hasCacheControlDirective('private') || $response->hasCacheControlDirective('no-cache')) {
                return;
            }

            if (!$maxAge = \Craft::$app->getResponse()->getMaxAge()) {
                $response->setSharedMaxAge($defaultMaxAge);
            }

            $headers->add(
                Plugin::getInstance()->getSettings()->getHeaderName(),
                implode($delimiter, $tags)
            );

            $plugin->trigger($plugin::EVENT_AFTER_SET_TAG_HEADER, new CacheResponseEvent([
                    'tags'       => $tags,
                    'maxAge'     => $maxAge ?? $defaultMaxAge,
                    'requestUrl' => \Craft::$app->getRequest()->getUrl()
                ]
            ));

        });
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
