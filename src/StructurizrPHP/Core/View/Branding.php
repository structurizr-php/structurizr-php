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

use Assert\Assert;
use StructurizrPHP\StructurizrPHP\Assertion;

class Branding
{
    private $logo;
    /**
     * @var Font
     */
    private $font;

    public static function hydrate(array $brandingData):self
    {
        $branding = new self();
        if (isset($brandingData['logo'])) {
            $branding->setLogo($brandingData['logo']);
        }

        return $branding;
    }

    public function setLogo(string $url)
    {
        Assertion::startsWith($url, 'data:image/');
        $this->logo = $url;
    }

    public function toArray(): array
    {
        return [
            'logo' => $this->logo,
        ];
    }
}
