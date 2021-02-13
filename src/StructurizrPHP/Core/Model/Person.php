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

use StructurizrPHP\Core\Model\Relationship\InteractionStyle;

final class Person extends StaticStructureElement
{
    /**
     * @var Location
     */
    private $location;

    public function __construct(string $id, Model $model)
    {
        parent::__construct($id, $model);

        $this->location = Location::unspecified();
        $this->setTags(new Tags(Tags::ELEMENT, Tags::PERSON));
    }

    public static function hydrate(array $personData, Model $model) : self
    {
        $person = new self(
            $personData['id'],
            $model
        );

        $model->idGenerator()->found($person->id());

        if (isset($personData['location'])) {
            $person->setLocation(Location::hydrate($personData['location']));
        }

        parent::hydrateElement($person, $personData);

        return $person;
    }

    /**
     * @param Location $location
     */
    public function setLocation(Location $location) : void
    {
        $this->location = $location;
    }

    public function uses(Element $softwareSystem, string $description, ?string $technology = null, ?InteractionStyle $interactionStyle = null) : Relationship
    {
        return $this->getModel()->addRelationship($this, $softwareSystem, $description, $technology, $interactionStyle);
    }

    public function interactsWith(self $destination, string $description, ?string $technology = null, ?InteractionStyle $interactionStyle = null) : Relationship
    {
        return $this->getModel()->addRelationship($this, $destination, $description, $technology, $interactionStyle);
    }

    public function getParent() : ?Element
    {
        return  null;
    }

    public function toArray() : array
    {
        return \array_merge(
            [
                'location' => $this->location->type(),
            ],
            parent::toArray()
        );
    }
}
