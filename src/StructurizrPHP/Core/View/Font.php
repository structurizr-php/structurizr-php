<?php

/*
 * This file is part of the Structurizr for PHP.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StructurizrPHP\StructurizrPHP\Core\View;

use StructurizrPHP\StructurizrPHP\Assertion;

class Font
{
    private $name;

    private $url;

    public function __construct(string $name, string $url)
    {
        $this->name = $name;
        $this->setUrl($url);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setUrl(string $url): void
    {
        Assertion::url($url);
        $this->url = \rtrim($url, '/');
    }
}
