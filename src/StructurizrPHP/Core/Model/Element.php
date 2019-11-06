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
     * @var Model
     */
    private $model;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var string|null
     */
    private $url;

    /**
     * @var Relationship[]
     */
    protected $relationships;

    public function __construct(string $id, Model $model)
    {
        parent::__construct($id);
        $this->model = $model;
        $this->relationships = [];
    }

    public function getCanonicalName() : string
    {
        return \mb_strtolower(\str_replace(self::CANONICAL_NAME_SEPARATOR, "", (string) $this->name));
    }

    public function description(): ?string
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

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): void
    {
        if ($url) {
            Assertion::url($url);
        }

        $this->url = $url;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function equals(self $element) : bool
    {
        return $this->id() === $element->id();
    }

    public function toArray() : array
    {
        $data = \array_merge(
            [
                'relationships' => \count($this->relationships)
                    ? \array_map(function (Relationship $relationship) {
                        return $relationship->toArray();
                    }, $this->relationships)
                    : null,
            ],
            parent::toArray()
        );

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
}
