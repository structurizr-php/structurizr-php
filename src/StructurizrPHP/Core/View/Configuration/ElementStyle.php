<?php

declare(strict_types=1);

/*
 * This file is part of the Structurizr SDK for PHP.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StructurizrPHP\StructurizrPHP\Core\View\Configuration;

final class ElementStyle
{
    public const DEFAULT_WIDTH = 450;
    public const DEFAULT_HEIGHT = 300;

    /**
     * @var string
     */
    private $tag;

    /**
     * @var Shape|null
     */
    private $shape;

    public function __construct(string $tag)
    {
        $this->tag = $tag;
    }

    public function setShape(Shape $shape) : void
    {
        $this->shape = $shape;
    }

    public function toArray() : array
    {
        return [
            'tag' => $this->tag,
            'shape' => ($this->shape) ? $this->shape->name() : null,
        ];
    }
}
