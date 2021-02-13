<?php

declare(strict_types=1);

/*
 * This file is part of the Structurizr for PHP.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StructurizrPHP\Core\Model;

use StructurizrPHP\Core\Assertion;

abstract class ModelItem
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Tags
     */
    private $tags;

    /**
     * @var Properties
     */
    private $properties;

    public function __construct(string $id)
    {
        Assertion::integerish($id);

        $this->id = $id;
        $this->tags = new Tags();
        $this->properties = new Properties();
    }

    public static function hydrateModelItem(self $modelItem, array $modelItemData, Model $model) : void
    {
        $model->idGenerator()->found($modelItemData['id']);

        $modelItem->id = $modelItemData['id'];

        if (isset($modelItemData['tags'])) {
            $modelItem->tags = new Tags(...\explode(', ', $modelItemData['tags']));
        }

        if (isset($modelItemData['properties'])) {
            $properties = new Properties();

            if (\is_array($modelItemData['properties'])) {
                foreach ($modelItemData['properties'] as $key => $value) {
                    $properties->addProperty(new Property($key, $value));
                }
            }

            $modelItem->properties = $properties;
        }
    }

    public function id() : string
    {
        return $this->id;
    }

    public function setId(string $id) : void
    {
        $this->id=$id;
    }

    public function setTags(Tags $tags) : void
    {
        $this->tags = $tags;
    }

    /**
     * @param string ...$tags
     */
    public function addTags(string ...$tags) : void
    {
        foreach ($tags as $tag) {
            $this->tags->add($tag);
        }
    }

    public function setProperties(Properties $properties) : void
    {
        $this->properties = $properties;
    }

    public function equals(self $modelItem) : bool
    {
        return $this->id() === $modelItem->id();
    }

    public function toArray() : array
    {
        return [
            'id' => $this->id,
            'tags' => $this->tags->toArray(),
            'properties' => $this->properties->toArray(),
        ];
    }
}
