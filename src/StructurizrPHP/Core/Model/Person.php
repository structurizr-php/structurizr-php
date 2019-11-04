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

    public function __construct(string $id, string $name, string $description, Location $location, Model $model)
    {
        parent::__construct($id, $name, $description, $model);
        $this->location = $location;
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
     * @psalm-suppress InvalidArgument
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    public static function hydrate(array $personData, Model $model) : self
    {
        $person = new self(
            $personData['id'],
            $personData['name'],
            $personData['description'],
            Location::hydrate($personData['location']),
            $model
        );

        $model->idGenerator()->found($person->id());

        if (\array_key_exists('tags', $personData)) {
            $person->setTags(new Tags(...\explode(', ', $personData['tags'])));
        }

        if (\array_key_exists('properties', $personData)) {
            $properties = new Properties();
            if (\is_array($personData['properties'])) {
                foreach ($personData['properties'] as $key => $value) {
                    $properties->addProperty(new Property($key, $value));
                }
            }

            $person->setProperties($properties);
        }

        if (\array_key_exists('relationships', $personData)) {
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
