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

use Thunder\Platenum\Enum\ConstantsEnumTrait;

/**
 * @method static static markdown();
 * @method static static asciiDoc();
 */
final class Format
{
    private const markdown = 'Markdown';

    private const asciiDoc = 'AsciiDoc';

    use ConstantsEnumTrait;

    public function name() : string
    {
        return (string) $this->getValue();
    }

    public static function hydrate(string $name) : self
    {
        return static::fromValue($name);
    }
}
