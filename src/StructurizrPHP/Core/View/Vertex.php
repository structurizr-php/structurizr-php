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

final class Vertex
{
    /**
     * @var int
     */
    private $x;

    /**
     * @var int
     */
    private $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public static function hydrate(array $vertexData) : self
    {
        return new self((int) $vertexData['x'], (int) $vertexData['y']);
    }

    public function x() : int
    {
        return $this->x;
    }

    /**
     * @param int $x
     */
    public function setX(int $x) : void
    {
        $this->x = $x;
    }

    public function y() : int
    {
        return $this->y;
    }

    /**
     * @param int $y
     */
    public function setY(int $y) : void
    {
        $this->y = $y;
    }

    public function toArray() : array
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
        ];
    }
}
