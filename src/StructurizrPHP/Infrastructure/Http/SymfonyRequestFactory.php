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

namespace StructurizrPHP\StructurizrPHP\Infrastructure\Http;

use Nyholm\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use StructurizrPHP\StructurizrPHP\Http\RequestFactory;

final class SymfonyRequestFactory implements RequestFactory
{
    public function create(string $uri, string $method, array $headers, ?string $body): RequestInterface
    {
        return new Request(
            $method,
            $uri,
            $headers,
            $body,
            $version = '1.1'
        );
    }
}
