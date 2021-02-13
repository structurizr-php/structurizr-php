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

final class DecisionStatus
{
    private const PROPOSED = 'Proposed';

    private const ACCEPTED = 'Accepted';

    private const SUPERSEDED = 'Superseded';

    private const DEPRECATED = 'Deprecated';

    private const REJECTED = 'Rejected';

    private $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function proposed() : self
    {
        return new self(self::PROPOSED);
    }

    public static function accepted() : self
    {
        return new self(self::ACCEPTED);
    }

    public static function superseded() : self
    {
        return new self(self::SUPERSEDED);
    }

    public static function deprecated() : self
    {
        return new self(self::DEPRECATED);
    }

    public static function rejected() : self
    {
        return new self(self::REJECTED);
    }

    public static function hydrate(string $status) : self
    {
        Assertion::inArray($status, [self::PROPOSED, self::ACCEPTED, self::SUPERSEDED, self::DEPRECATED, self::REJECTED]);

        return new self($status);
    }

    public function __toString() : string
    {
        return $this->name;
    }
}
