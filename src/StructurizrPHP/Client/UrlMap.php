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

final class UrlMap
{
    /**
     * @var string
     */
    private $serverUrl;

    public function __construct(string $serverUrl)
    {
        Assertion::url($serverUrl);

        $this->serverUrl = \rtrim($serverUrl, '/');
    }

    public function workspaceUrl(string $workspaceId) : string
    {
        return $this->serverUrl . $this->workspaceURIPath($workspaceId);
    }

    public function workspaceURIPath(string $workspaceId) : string
    {
        return \sprintf('/workspace/%s', $workspaceId);
    }
}
