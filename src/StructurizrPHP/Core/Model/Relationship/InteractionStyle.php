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

namespace StructurizrPHP\Core\Model\Relationship;

use StructurizrPHP\Core\Assertion;

final class InteractionStyle
{
    private $style;

    private function __construct(string $type)
    {
        $this->style = $type;
    }

    public static function synchronous() : self
    {
        return new self('Synchronous');
    }

    public static function asynchronous() : self
    {
        return new self('Asynchronous');
    }

    public static function hydrate(string $style) : self
    {
        Assertion::inArray($style, ['Synchronous', 'Asynchronous']);

        return new self($style);
    }

    public function style() : string
    {
        return $this->style;
    }
}
