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

namespace StructurizrPHP\Core\View;

use StructurizrPHP\Core\Model\Relationship;

final class RelationshipView
{
    private const START_OF_LINE = 0;

    private const END_OF_LINE = 100;

    /**
     * @var null|string
     */
    private $description;

    /**
     * @var null|int
     */
    private $position;

    /**
     * @var null|string
     */
    private $order;

    /**
     * @var Vertex[]
     */
    private $vertices;

    /**
     * @var null|Routing
     */
    private $routing;

    /**
     * @var Relationship
     */
    private $relationship;

    public function __construct(Relationship $relationship)
    {
        $this->relationship = $relationship;
        $this->vertices = [];
    }

    public static function hydrate(Relationship $relationship, array $viewData) : self
    {
        $view = new self($relationship);

        if (isset($viewData['description'])) {
            $view->setDescription($viewData['description']);
        }

        if (isset($viewData['order'])) {
            $view->setOrder((string) $viewData['order']);
        }

        if (isset($viewData['position'])) {
            $view->setPosition($viewData['position']);
        }

        if (isset($viewData['routing'])) {
            $view->setRouting(Routing::hydrate($viewData['routing']));
        }

        if (isset($viewData['vertices'])) {
            $view->setVertices(...\array_map(
                function (array $vertexData) {
                    return Vertex::hydrate($vertexData);
                },
                $viewData['vertices']
            ));
        }

        return $view;
    }

    public function setPosition(?int $position) : void
    {
        if ($position === null) {
            $this->position = null;
        } elseif ($position < self::START_OF_LINE) {
            $this->position = self::START_OF_LINE;
        } elseif ($position > self::END_OF_LINE) {
            $this->position = self::END_OF_LINE;
        } else {
            $this->position = $position;
        }
    }

    public function getRelationship() : Relationship
    {
        return $this->relationship;
    }

    public function setDescription(?string $description) : void
    {
        $this->description = $description;
    }

    /**
     * @param Vertex ...$vertices
     */
    public function setVertices(Vertex ...$vertices) : void
    {
        $this->vertices = $vertices;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function getOrder() : ?string
    {
        return $this->order;
    }

    public function setOrder(string $order) : void
    {
        $this->order = $order;
    }

    /**
     * @param null|Routing $routing
     */
    public function setRouting(?Routing $routing) : void
    {
        $this->routing = $routing;
    }

    public function copyLayoutInformationFrom(?self $source) : void
    {
        if ($source !== null) {
            $this->setVertices(...$source->vertices);
            $this->setPosition($source->position);
            $this->setRouting($source->routing);
        }
    }

    public function toArray() : array
    {
        $data = [
            'id' => $this->relationship->id(),
            'vertices' => \array_map(
                function (Vertex $vertex) {
                    return $vertex->toArray();
                },
                $this->vertices
            ),
        ];

        if ($this->order) {
            $data['order'] = $this->order;
        }

        if ($this->description) {
            $data['description'] = $this->description;
        }

        if ($this->position !== null) {
            $data['position'] = $this->position;
        }

        if ($this->routing) {
            $data['routing'] = $this->routing->type();
        }

        return $data;
    }
}
