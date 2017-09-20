<?php
/**
 * Created by PhpStorm.
 * User: os
 * Date: 19.09.17
 * Time: 14:37
 */

namespace ostark\falcon;


class TagCollection
{
    protected $tags = [];

    public function add(string $tag)
    {
        $this->tags[] = $tag;
    }

    public function getAll()
    {
        return $this->tags;
    }

    public function addTagsFromElement(array $elementRawQueryResult = null)
    {
        if (!is_array($elementRawQueryResult)) {
            return;
        }

        foreach ($this->extractTags($elementRawQueryResult) as $tag) {
            $this->add($tag);
        }
    }

    protected function extractTags(array $elementRawQueryResult = null): array
    {
        $tags       = [];
        $properties = array_keys(Plugin::ELEMENT_PROPERTY_MAP);

        foreach ($properties as $prop) {
            if (isset($elementRawQueryResult[$prop]) && !is_null($elementRawQueryResult[$prop])) {
                $tags[] = Plugin::ELEMENT_PROPERTY_MAP[$prop] . $elementRawQueryResult[$prop];
            }
        }

        return $tags;
    }
}
