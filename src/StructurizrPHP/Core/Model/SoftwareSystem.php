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

use StructurizrPHP\Core\Exception\InvalidArgumentException;

/**
 * Represents a "software system" in the C4 model.
 */
final class SoftwareSystem extends StaticStructureElement
{
    /**
     * @var Location
     */
    private $location;

    /**
     * @var Container[]
     */
    private $containers;

    public function __construct(string $id, Model $model)
    {
        parent::__construct($id, $model);

        $this->location = Location::unspecified();
        $this->containers = [];
        $this->setTags(new Tags(Tags::ELEMENT, Tags::SOFTWARE_SYSTEM));
    }

    public static function hydrate(array $softwareSystemData, Model $model) : self
    {
        $softwareSystem = new self(
            $softwareSystemData['id'],
            $model
        );

        parent::hydrateElement($softwareSystem, $softwareSystemData);

        if (isset($softwareSystemData['location'])) {
            $softwareSystem->setLocation(Location::hydrate($softwareSystemData['location']));
        }

        if (isset($softwareSystemData['containers'])) {
            if (\is_array($softwareSystemData['containers'])) {
                // hydrate containers without relationships
                foreach ($softwareSystemData['containers'] as $containerData) {
                    $container = Container::hydrate($containerData, $softwareSystem, $model);
                    $softwareSystem->add($container);
                }
            }
        }

        return $softwareSystem;
    }

    public static function hydrateContainersRelationships(self $softwareSystem, array $softwareSystemData) : void
    {
        if (isset($softwareSystemData['containers'])) {
            if (\is_array($softwareSystemData['containers'])) {
                // hydrate containers missing relationships
                foreach ($softwareSystemData['containers'] as $containerData) {
                    Container::hydrateRelationships($softwareSystem->getContainer($containerData['id']), $containerData);

                    if (isset($containerData['components']) && \is_array($containerData['components'])) {
                        foreach ($containerData['components'] as $componentData) {
                            Component::hydrateRelationships($softwareSystem->getContainer($containerData['id'])->getComponent($componentData['id']), $componentData);
                        }
                    }
                }
            }
        }
    }

    public function getParent() : ?Element
    {
        return null;
    }

    public function add(Container $container) : void
    {
        $this->containers[] = $container;
    }

    /**
     * @return Container[]
     */
    public function getContainers() : array
    {
        return $this->containers;
    }

    public function getContainer(string $id) : Container
    {
        foreach ($this->containers as $container) {
            if ($container->id() === $id) {
                return $container;
            }
        }

        throw new InvalidArgumentException(\sprintf('Continer with id %s does not exists', $id));
    }

    public function addContainer(string $name, string $description, string $technology) : Container
    {
        return $this->getModel()->addContainer($this, $name, $description, $technology);
    }

    public function findContainerWithName(string $containerName) : ?Container
    {
        foreach ($this->containers as $container) {
            if ($container->getName() === $containerName) {
                return $container;
            }
        }

        return null;
    }

    /**
     * @param Location $location
     */
    public function setLocation(Location $location) : void
    {
        $this->location = $location;
    }

    public function toArray() : array
    {
        return \array_merge(
            [
                'location' => $this->location->type(),
                'containers' => \array_map(
                    function (Container $container) {
                        return $container->toArray();
                    },
                    $this->containers
                ),
            ],
            parent::toArray()
        );
    }
}
