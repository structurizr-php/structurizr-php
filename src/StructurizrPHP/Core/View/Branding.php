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

namespace StructurizrPHP\StructurizrPHP\Core\View;

use StructurizrPHP\StructurizrPHP\Assertion;

final class Branding
{
    /**
     * @var string|null
     */
    private $logo;

    /**
     * @var Font|null
     */
    private $font;

    public function __construct(?string $logo = null, ?Font $font = null)
    {
        if ($this->logo) {
            $this->setLogo($logo);
        }

        $this->font = $font;
    }

    public function setLogo(string $url) : void
    {
        Assertion::startsWith($url, 'data:image/');
        $this->logo = $url;
    }

    public function toArray() : array
    {
        $data = [];

        if ($this->logo) {
            $data['logo'] = $this->logo;
        }

        if ($this->font) {
            $data['font'] = $this->font->toArray();
        }

        return $data;
    }

    public static function hydrate(array $brandingData) : self
    {
        $branding = new self();

        if (isset($brandingData['logo'])) {
            $branding->setLogo($brandingData['logo']);
        }

        if (isset($brandingData['font'])) {
            $branding->font = Font::hydrate($brandingData['font']);
        }

        return $branding;
    }
}
