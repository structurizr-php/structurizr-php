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

namespace StructurizrPHP\StructurizrPHP\SDK;

use StructurizrPHP\StructurizrPHP\Assertion;
use StructurizrPHP\StructurizrPHP\Core\Workspace;

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

    public function workspaceUrl(Workspace $workspace) : string
    {
        return $this->serverUrl . $this->workspaceURIPath($workspace);
    }

    public function workspaceURIPath(Workspace $workspace) : string
    {
        return \sprintf('/workspace/%s', $workspace->id());
    }
}
