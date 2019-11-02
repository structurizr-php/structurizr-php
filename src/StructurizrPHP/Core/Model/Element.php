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

use StructurizrPHP\StructurizrPHP\Assertion;

abstract class Element extends ModelItem
{
    private const CANONICAL_NAME_SEPARATOR = "/";

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var Relationship[]
     */
    private $relationships;

    public function __construct(string $id, string $name, string $description, Model $model)
    {
        Assertion::string($name);
        Assertion::string($description);

        parent::__construct($id);
        $this->name = $name;
        $this->description = $description;
        $this->model = $model;
        $this->relationships = [];
    }

    public function description(): string
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
    public function relationships(): array
    {
        return $this->relationships;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function equals(self $element) : bool
    {
        return $this->id() === $element->id();
    }

    public function toArray() : array
    {
        return \array_merge(
            [
                'name' => $this->name,
                'description' => $this->description,
                'relationships' => \count($this->relationships)
                    ? \array_map(function (Relationship $relationship) {
                        return $relationship->toArray();
                    }, $this->relationships)
                    : null,
            ],
            parent::toArray()
        );
    }
}
