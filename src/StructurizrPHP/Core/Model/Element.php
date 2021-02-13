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
use StructurizrPHP\Core\Exception\RuntimeException;

abstract class Element extends ModelItem
{
    protected const CANONICAL_NAME_SEPARATOR = '/';

    /**
     * @var Relationship[]
     */
    protected $relationships;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var null|string
     */
    private $name;

    /**
     * @var null|string
     */
    private $description;

    /**
     * @var null|string
     */
    private $url;

    public function __construct(string $id, Model $model)
    {
        parent::__construct($id);
        $this->model = $model;
        $this->name = '';
        $this->relationships = [];
    }

    public static function hydrateElement(self $element, array $elementData) : void
    {
        $element->model->addElementToInternalStructures($element);

        if (isset($elementData['url'])) {
            $element->url = $elementData['url'];
        }

        if (isset($elementData['name'])) {
            $element->name = $elementData['name'];
        }

        if (isset($elementData['description'])) {
            $element->description = $elementData['description'];
        }

        parent::hydrateModelItem($element, $elementData, $element->model);
    }

    public static function hydrateRelationships(self $element, array $elementData) : void
    {
        if (isset($elementData['relationships'])) {
            if (\is_array($elementData['relationships'])) {
                foreach ($elementData['relationships'] as $relationshipData) {
                    $relationship = Relationship::hydrate($relationshipData, $element, $element->getModel());
                    $element->relationships[] = $relationship;
                }
            }
        }

        // sort relationships by ID
        if (isset($elementData['relationships'])) {
            \usort(
                $elementData['relationships'],
                function (array $relationshipA, array $relationshipB) {
                    return (int) $relationshipA['id'] > (int) $relationshipB['id'] ? 1 : 0;
                }
            );
        }
    }

    public function getName() : string
    {
        return $this->name;
    }

    abstract public function getParent() : ?self;

    public function getCanonicalName() : string
    {
        return $this->formatForCanonicalName($this->name);
    }

    public function description() : ?string
    {
        return $this->description;
    }

    public function addRelationship(Relationship $relationship) : void
    {
        $this->relationships[] = $relationship;
    }

    /**
     * @return Relationship[]
     */
    public function getRelationships() : array
    {
        return $this->relationships;
    }

    public function getEfferentRelationshipWith(self $element) : Relationship
    {
        foreach ($this->relationships as $relationship) {
            if ($relationship->getDestination()->equals($element)) {
                return $relationship;
            }
        }

        throw new RuntimeException(\sprintf('There is no efferent relationship between %s[#%s] and %s[#%s]', $this->getName(), $this->id(), $element->getName(), $element->id()));
    }

    public function getModel() : Model
    {
        return $this->model;
    }

    public function setName(?string $name) : void
    {
        $this->name = $name;
    }

    public function setUrl(?string $url) : void
    {
        if ($url) {
            Assertion::url($url);
        }

        $this->url = $url;
    }

    public function setDescription(?string $description) : void
    {
        $this->description = $description;
    }

    public function hasEfferentRelationshipWith(self $destination) : bool
    {
        try {
            return $this->getEfferentRelationshipWith($destination) !== null;
        } catch (RuntimeException $e) {
            return false;
        }
    }

    public function equalTo(self $element) : bool
    {
        return $this->id() === $element->id();
    }

    public function toArray() : array
    {
        $data = parent::toArray();

        if (\count($this->relationships)) {
            $data['relationships'] = \array_map(
                function (Relationship $relationship) {
                    return $relationship->toArray();
                },
                $this->relationships
            );
        }

        if ($this->name) {
            $data['name'] = $this->name;
        }

        if ($this->description) {
            $data['description'] = $this->description;
        }

        if ($this->url) {
            $data['url'] = $this->url;
        }

        return $data;
    }

    protected function formatForCanonicalName(string $name) : string
    {
        return \mb_strtolower(\str_replace(self::CANONICAL_NAME_SEPARATOR, '', $name));
    }
}
