<?php

declare(strict_types=1);

/*
 * This file is part of the Structurizr SDK for PHP.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StructurizrPHP\StructurizrPHP\Http;

use Psr\Http\Message\RequestInterface;

interface RequestFactory
{
    public function create(string $uri, string $method, array $headers, ?string $body) : RequestInterface;
}
