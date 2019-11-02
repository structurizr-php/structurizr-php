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

namespace StructurizrPHP\StructurizrPHP\Core\Model;

final class Tags
{
    public const ELEMENT = "Element";
    public const PERSON = "Person";
    public const SOFTWARE_SYSTEM = "Software System";

    private $tags;

    public function __construct(string ...$tags)
    {
        $this->tags = $tags;
    }

    public function toArray() : ?string
    {
        return \count($this->tags)
                ? \implode(", ", $this->tags)
                : null;
    }
}
