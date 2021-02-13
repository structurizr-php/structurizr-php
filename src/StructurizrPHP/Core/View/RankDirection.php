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

final class RankDirection
{
    private $direction;

    private function __construct(string $direction)
    {
        $this->direction = $direction;
    }

    public static function topBottom() : self
    {
        return new self('TopBottom');
    }

    public static function bottomTop() : self
    {
        return new self('BottomTop');
    }

    public static function leftRight() : self
    {
        return new self('LeftRight');
    }

    public static function rightLeft() : self
    {
        return new self('RightLeft');
    }

    public static function hydrate(string $direction) : self
    {
        return new self($direction);
    }

    public function direction() : string
    {
        return $this->direction;
    }
}
