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

namespace StructurizrPHP\Core\Documentation;

use StructurizrPHP\Core\Assertion;

final class Format
{
    private const MARKDOWN = 'Markdown';

    private const ASCII_DOC = 'AsciiDoc';

    /**
     * @var string
     */
    private $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function markdown() : self
    {
        return new self(self::MARKDOWN);
    }

    public static function asciiDoc() : self
    {
        return new self(self::ASCII_DOC);
    }

    public static function hydrate(string $name) : self
    {
        Assertion::inArray($name, [self::MARKDOWN, self::ASCII_DOC]);

        return new self($name);
    }

    public function name() : string
    {
        return $this->name;
    }
}
