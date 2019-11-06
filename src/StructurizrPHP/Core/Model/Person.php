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

namespace StructurizrPHP\StructurizrPHP\Core\Model;

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

    public function toArray() : array
    {
        return \array_merge(
            [
                'location' => $this->location->type(),
            ],
            parent::toArray()
        );
    }

    /**
     * @param Location $location
     */
    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }

    /**
     * @psalm-suppress InvalidArgument
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    public static function hydrate(array $personData, Model $model) : self
    {
        $person = new self(
            $personData['id'],
            $model
        );

        $model->idGenerator()->found($person->id());

        if (isset($personData['name'])) {
            $person->setName($personData['name']);
        }

        if (isset($personData['description'])) {
            $person->setDescription($personData['description']);
        }

        if (isset($personData['location'])) {
            $person->setLocation(Location::hydrate($personData['location']));
        }

        if (isset($personData['tags'])) {
            $person->setTags(new Tags(...\explode(', ', $personData['tags'])));
        }

        if (isset($personData['url'])) {
            $person->setUrl($personData['url']);
        }

        if (isset($personData['properties'])) {
            $properties = new Properties();
            if (\is_array($personData['properties'])) {
                foreach ($personData['properties'] as $key => $value) {
                    $properties->addProperty(new Property($key, $value));
                }
            }

            $person->setProperties($properties);
        }

        if (isset($personData['relationships'])) {
            if (\is_array($personData['relationships'])) {
                foreach ($personData['relationships'] as $relationshipData) {
                    $relationship = Relationship::hydrate($relationshipData, $person, $model);
                    $person->addRelationship($relationship);
                }
            }
        }

        // sort relationships by ID
        \usort(
            $person->relationships,
            function (Relationship $relationshipA, Relationship $relationshipB) {
                return (int)$relationshipA->id() > (int)$relationshipB->id() ? 1 : 0;
            }
        );

        return $person;
    }
}
