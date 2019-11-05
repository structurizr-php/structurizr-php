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

namespace StructurizrPHP\StructurizrPHP\Core\Model\Configuration;

final class Role
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    private function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return static
     */
    public static function readWrite(): self
    {
        return new self('ReadWrite');
    }

    /**
     * @return static
     */
    public static function readOnly(): self
    {
        return new self('ReadOnly');
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->name;
    }
}
