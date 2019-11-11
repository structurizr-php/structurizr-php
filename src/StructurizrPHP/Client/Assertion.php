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

namespace StructurizrPHP\StructurizrPHP\Client;

use Assert\Assertion as BaseAssertion;
use StructurizrPHP\StructurizrPHP\Core\Exception\AssertionException;

final class Assertion extends BaseAssertion
{
    protected static $exceptionClass = AssertionException::class;

    public static function hexColorCode(string $colorAsString) : bool
    {
        self::regex($colorAsString, '/^#[A-Fa-f0-9]{6}/');

        return true;
    }
}
