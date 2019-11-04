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

final class SoftwareSystem extends StaticStructureElement
{
    /**
     * @var Location
     */
    private $location;

    public function __construct(string $id, string $name, string $description, Location $location, Model $model)
    {
        parent::__construct($id, $name, $description, $model);
        $this->location = $location;
        $this->setTags(new Tags(Tags::ELEMENT, Tags::SOFTWARE_SYSTEM));
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
    public static function hydrate(array $softwareSystemData, Model $model) : self
    {
        $softwareSystem = new self(
            $softwareSystemData['id'],
            $softwareSystemData['name'],
            $softwareSystemData['description'],
            Location::hydrate($softwareSystemData['location']),
            $model
        );

        $model->idGenerator()->found($softwareSystem->id());

        if (\array_key_exists('tags', $softwareSystemData)) {
            $softwareSystem->setTags(new Tags(...\explode(', ', $softwareSystemData['tags'])));
        }

        if (\array_key_exists('properties', $softwareSystemData)) {
            $properties = new Properties();
            if (\is_array($softwareSystemData['properties'])) {
                foreach ($softwareSystemData['properties'] as $key => $value) {
                    $properties->addProperty(new Property($key, $value));
                }
            }

            $softwareSystem->setProperties($properties);
        }

        if (\array_key_exists('relationships', $softwareSystemData)) {
            if (\is_array($softwareSystemData['relationships'])) {
                foreach ($softwareSystemData['relationships'] as $relationshipData) {
                    $relationship = Relationship::hydrate($relationshipData, $softwareSystem, $model);
                    $softwareSystem->addRelationship($relationship);
                }
            }
        }

        // sort relationships by ID
        \usort(
            $softwareSystem->relationships,
            function (Relationship $relationshipA, Relationship $relationshipB) {
                return (int) $relationshipA->id() > (int)$relationshipB->id() ? 1 : 0;
            }
        );

        return $softwareSystem;
    }
}
