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

namespace StructurizrPHP\StructurizrPHP\Core\Documentation;

final class Format
{
    private $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name() : string
    {
        return $this->name;
    }

    public static function markdown() : self
    {
        return new self('Markdown');
    }

    public static function asciiDoc() : self
    {
        return new self('AsciiDoc');
    }

    public static function hydrate(string $name) : self
    {
        return new self($name);
    }
}
