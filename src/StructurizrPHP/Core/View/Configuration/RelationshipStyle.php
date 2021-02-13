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

namespace StructurizrPHP\Core\View\Configuration;

use StructurizrPHP\Core\Assertion;
use StructurizrPHP\Core\View\Routing;

final class RelationshipStyle
{
    private const START_OF_LINE = 0;

    private const END_OF_LINE = 100;

    /**
     * @var string
     *
     * The name of the tag to which this style applies
     */
    private $tag;

    /**
     * @var null|int
     *
     * The thickness of the line, in pixels
     */
    private $thickness;

    /**
     * @var null|string
     *
     * The colour of the line, as a HTML hex value (e.g. #123456).
     */
    private $color;

    /**
     * @var null|int
     *
     * The font size of the annotation, in pixels
     */
    private $fontSize;

    /**
     * @var null|int
     *
     * The width of the annotation, in pixels
     */
    private $width;

    /**
     * @var bool
     *
     * Whether the line should be dashed or not
     */
    private $dashed;

    /**
     * @var null|routing
     *
     * The routing algorithm used when rendering lines
     */
    private $routing;

    /**
     * @var null|int
     *
     * The position of the annotation along the line; 0 (start) to 100 (end)
     */
    private $position;

    /**
     * @var null|int
     *
     * The opacity of the line/text; 0 to 100
     */
    private $opacity;

    public function __construct(string $tag)
    {
        $this->tag = $tag;
    }

    public static function hydrate(array $relationshipData) : self
    {
        $relationship = new self($relationshipData['tag']);

        if (isset($relationshipData['thickness'])) {
            $relationship->thickness = (int) $relationshipData['thickness'];
        }

        if (isset($relationshipData['fontSize'])) {
            $relationship->fontSize = (int) $relationshipData['fontSize'];
        }

        if (isset($relationshipData['color'])) {
            $relationship->color = (string) $relationshipData['color'];
        }

        if (isset($relationshipData['width'])) {
            $relationship->width = (int) $relationshipData['width'];
        }

        if (isset($relationshipData['dashed'])) {
            $relationship->dashed = (bool) $relationshipData['dashed'];
        }

        if (isset($relationshipData['opacity'])) {
            $relationship->opacity = (int) $relationshipData['opacity'];
        }

        if (isset($relationshipData['routing'])) {
            $relationship->routing = Routing::hydrate($relationshipData['routing']);
        }

        if (isset($relationshipData['position'])) {
            $relationship->position = (int) $relationshipData['position'];
        }

        return $relationship;
    }

    public function thickness(int $thickness) : self
    {
        Assertion::greaterThan($thickness, 0);

        $this->thickness = $thickness;

        return $this;
    }

    public function color(string $color) : self
    {
        Assertion::hexColorCode($color);

        $this->color = \strtolower($color);

        return $this;
    }

    public function dashed(bool $dashed) : self
    {
        $this->dashed = $dashed;

        return $this;
    }

    public function fontSize(int $fontSize) : self
    {
        Assertion::greaterThan($fontSize, 0);

        $this->fontSize = $fontSize;

        return $this;
    }

    public function width(int $width) : self
    {
        Assertion::greaterThan($width, 0);

        $this->width = $width;

        return $this;
    }

    public function opacity(int $opacity) : self
    {
        Assertion::greaterOrEqualThan($opacity, 0);
        Assertion::lessOrEqualThan($opacity, 100);

        $this->opacity = $opacity;

        return $this;
    }

    public function setRouting(Routing $routing) : self
    {
        $this->routing = $routing;

        return $this;
    }

    public function setPosition(?int $position) : self
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

        return $this;
    }

    public function position(?int $position) : self
    {
        $this->setPosition($position);

        return $this;
    }

    public function toArray() : array
    {
        $data = ['tag' => $this->tag];

        if ($this->thickness) {
            $data['thickness'] = $this->thickness;
        }

        if ($this->fontSize) {
            $data['fontSize'] = $this->fontSize;
        }

        if ($this->color) {
            $data['color'] = $this->color;
        }

        if ($this->width) {
            $data['width'] = $this->width;
        }

        if ($this->dashed) {
            $data['dashed'] = $this->dashed;
        }

        if ($this->opacity) {
            $data['opacity'] = $this->opacity;
        }

        if ($this->routing) {
            $data['routing'] = $this->routing->type();
        }

        if ($this->position) {
            $data['position'] = $this->position;
        }

        return $data;
    }
}
