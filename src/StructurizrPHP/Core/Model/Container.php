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

/**
 * Represents a "container" in the C4 model.
 */
final class Container extends StaticStructureElement
{
    /**
     * @var SoftwareSystem
     */
    private $parent;

    /**
     * @var null|string
     */
    private $technology;

    /**
     * @var Component[]
     */
    private $components;

    public function __construct(string $id, SoftwareSystem $parent, Model $model)
    {
        parent::__construct($id, $model);
        $this->parent = $parent;
        $this->components = [];
    }

    public static function hydrate(array $containerData, SoftwareSystem $parent, Model $model) : self
    {
        $container = new self($containerData['id'], $parent, $model);

        if (isset($containerData['technology'])) {
            $container->setTechnology($containerData['technology']);
        }

        if (isset($containerData['components'])) {
            foreach ($containerData['components'] as $componentData) {
                $component = Component::hydrate(
                    $componentData,
                    $model,
                    $container
                );

                $container->components[] = $component;
            }
        }
        parent::hydrateElement($container, $containerData);

        return $container;
    }

    /**
     * @param SoftwareSystem $parent
     */
    public function setParent(SoftwareSystem $parent) : void
    {
        $this->parent = $parent;
    }

    /**
     * @return null|Element|SoftwareSystem
     */
    public function getParent() : ?Element
    {
        return $this->parent;
    }

    public function getSoftwareSystem() : SoftwareSystem
    {
        return $this->parent;
    }

    /**
     * @param null|string $technology
     */
    public function setTechnology(?string $technology) : void
    {
        $this->technology = $technology;
    }

    public function uses(self $container, string $description, ?string $technology = null) : Relationship
    {
        return $this->getModel()->addRelationship($this, $container, $description, $technology);
    }

    public function addComponent(string $name, string $type, string $description, ?string $technology = null) : Component
    {
        return $this->getModel()->addComponentOfType($this, $name, $type, $description, $technology);
    }

    public function getComponentWithName(string $name) : ?Component
    {
        Assertion::notEmpty($name, 'A component name must be provided.');

        $component = \current(\array_filter(
            $this->components,
            function (Component $component) use ($name) {
                return $component->getName() === $name;
            }
        ));

        return $component ? $component : null;
    }

    public function getComponent(string $id) : ?Component
    {
        $component = \current(\array_filter(
            $this->components,
            function (Component $component) use ($id) {
                return $component->id() === $id;
            }
        ));

        return $component ? $component : null;
    }

    /**
     * @return Component[]
     */
    public function getComponents() : array
    {
        return $this->components;
    }

    public function add(Component $component) : void
    {
        if ($this->getComponentWithName($component->getName()) === null) {
            $this->components[] = $component;
        }
    }

    public function toArray() : array
    {
        $data = \array_merge(
            [
                'description' => $this->description(),
                'technology' => $this->technology,
            ],
            parent::toArray()
        );

        if (\count($this->components)) {
            $data['components'] = \array_map(
                function (Component $component) {
                    return $component->toArray();
                },
                $this->components
            );
        }

        return $data;
    }
}
