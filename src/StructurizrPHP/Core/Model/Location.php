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

namespace StructurizrPHP\Core\Model;

use StructurizrPHP\Core\Assertion;

final class Location
{
    private $type;

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function external() : self
    {
        return new self('External');
    }

    public static function internal() : self
    {
        return new self('Internal');
    }

    public static function unspecified() : self
    {
        return new self('Unspecified');
    }

    public static function hydrate(string $type) : self
    {
        Assertion::inArray($type, ['External', 'Internal', 'Unspecified']);

        return new self($type);
    }

    public function type() : string
    {
        return $this->type;
    }
}
