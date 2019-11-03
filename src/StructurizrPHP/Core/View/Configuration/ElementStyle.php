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

namespace StructurizrPHP\StructurizrPHP\Core\View\Configuration;

use StructurizrPHP\StructurizrPHP\Assertion;

final class ElementStyle
{
    public const DEFAULT_WIDTH = 450;
    public const DEFAULT_HEIGHT = 300;

    /**
     * @var string
     */
    private $tag;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var int|null
     */
    private $opacity;

    /**
     * @var string|null
     */
    private $color;

    /**
     * @var string|null
     */
    private $background;

    /**
     * @var int|null
     */
    private $fontSize;

    /**
     * @var Border
     */
    private $border;

    /**
     * @var Shape|null
     */
    private $shape;

    /**
     * @var bool
     */
    private $metadata;

    /**
     * @var string|null
     */
    private $icon;

    /**
     * @var string|null
     */
    private $description;

    public function __construct(string $tag)
    {
        $this->tag = $tag;
        $this->width = self::DEFAULT_WIDTH;
        $this->height = self::DEFAULT_HEIGHT;
        $this->border = Border::solid();
        $this->metadata = true;
    }

    public function shape(Shape $shape) : self
    {
        $this->shape = $shape;

        return $this;
    }

    public function width(int $width) : self
    {
        Assertion::greaterThan($width, 0);

        $this->width = $width;

        return $this;
    }

    public function height(int $height) : self
    {
        Assertion::greaterThan($height, 0);

        $this->height = $height;

        return $this;
    }

    public function color(string $color) : self
    {
        Assertion::hexColorCode($color);

        $this->color = $color;

        return $this;
    }

    public function background(string $color) : self
    {
        Assertion::hexColorCode($color);

        $this->background = $color;

        return $this;
    }

    public function fontSize(int $fontSize) : self
    {
        Assertion::greaterThan($fontSize, 0);

        $this->fontSize = $fontSize;

        return $this;
    }

    public function opacity(int $opacity) : self
    {
        Assertion::greaterOrEqualThan($opacity, 0);
        Assertion::lessOrEqualThan($opacity, 100);

        $this->opacity = $opacity;

        return $this;
    }

    public function setBorder(Border $border) : self
    {
        $this->border = $border;

        return $this;
    }

    public function setMetadata(bool $metadata) : void
    {
        $this->metadata = $metadata;
    }

    public function setDescription(string $description) : void
    {
        Assertion::notEmpty($description);

        $this->description = $description;
    }

    /**
     * Sets a Base64 encoded PNG/JPG/GIF file).
     */
    public function iconBase64(string $icon) : self
    {
        Assertion::startsWith($icon, 'data:image/');

        $this->icon = $icon;

        return $this;
    }

    /**
     * Sets a Base64 encoded PNG/JPG/GIF file).
     */
    public function iconUrl(string $icon) : self
    {
        Assertion::url($icon);

        $this->icon = $icon;

        return $this;
    }

    public function toArray() : array
    {
        $data = [
            'tag' => $this->tag,
            'shape' => ($this->shape) ? $this->shape->name() : null,
            'fontSize' => $this->fontSize ? $this->fontSize : null,
            'opacity' => $this->opacity ? $this->opacity : null,
            'border' => $this->border->type(),
            'width' => $this->width,
            'height' => $this->height,
            'metadata' => $this->metadata,
            'icon' => $this->icon ? $this->icon : null,
            'description' => $this->description ? $this->description : null,
        ];

        if ($this->color) {
            $data['color'] = $this->color;
        }

        if ($this->background) {
            $data['background'] = $this->background;
        }

        return $data;
    }
}
