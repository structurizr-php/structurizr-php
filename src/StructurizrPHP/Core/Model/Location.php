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
    public const TYPE_EXTERNAL = 'External';

    public const TYPE_INTERNAL = 'Internal';

    public const TYPE_UNSPECIFIED = 'Unspecified';

    /**
     * @var string
     */
    private $type;

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function external() : self
    {
        return new self(self::TYPE_EXTERNAL);
    }

    public static function internal() : self
    {
        return new self(self::TYPE_INTERNAL);
    }

    public static function unspecified() : self
    {
        return new self(self::TYPE_UNSPECIFIED);
    }

    public static function hydrate(string $type) : self
    {
        Assertion::inArray($type, [self::TYPE_EXTERNAL, self::TYPE_INTERNAL, self::TYPE_UNSPECIFIED]);

        return new self($type);
    }

    public function type() : string
    {
        return $this->type;
    }
}
