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

namespace StructurizrPHP\Client\Exception;

final class AssertionException extends InvalidArgumentException
{
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}
