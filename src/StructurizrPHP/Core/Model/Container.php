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
     * @var string|null
     */
    private $technology;

    public function __construct(string $id, SoftwareSystem $parent, Model $model)
    {
        parent::__construct($id, $model);
        $this->parent = $parent;
    }

    /**
     * @param SoftwareSystem $parent
     */
    public function setParent(SoftwareSystem $parent): void
    {
        $this->parent = $parent;
    }

    public function getParent(): SoftwareSystem
    {
        return $this->parent;
    }

    /**
     * @param string|null $technology
     */
    public function setTechnology(?string $technology): void
    {
        $this->technology = $technology;
    }

    public function toArray(): array
    {
        $data = \array_merge(
            [
                'description' => $this->description(),
                'technology' => $this->technology,
            ],
            parent::toArray()
        );

        return $data;
    }

    /**
     * @psalm-suppress MixedArgument
     */
    public static function hydrate(array $containerData, SoftwareSystem $parent, Model $model) : self
    {
        $container = new self($containerData['id'], $parent, $model);

        $model->idGenerator()->found((string) $containerData['id']);

        if (isset($containerData['name'])) {
            $container->setName($containerData['name']);
        }

        if (isset($containerData['technology'])) {
            $container->setTechnology($containerData['technology']);
        }

        if (isset($containerData['description'])) {
            $container->setDescription($containerData['description']);
        }

        return $container;
    }
}
