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

use StructurizrPHP\StructurizrPHP\Core\View\Configuration\Styles;

final class Configuration
{
    /**
     * @var Styles
     */
    private $styles;

    /**
     * @var string|null
     */
    private $lastSavedView;
    private $branding;

    public function __construct()
    {
        $this->styles = new Styles();
        $this->branding = new Branding();
    }

    public function getStyles() : Styles
    {
        return $this->styles;
    }

    public function copyConfigurationFrom(self $configuration) : void
    {
        $this->lastSavedView = $configuration->lastSavedView;
    }

    public function toArray() : array
    {
        return [
            'lastSavedView' => $this->lastSavedView,
            'styles' => $this->styles->toArray(),
            'branding' => $this->branding->toArray(),
        ];
    }

    public static function hydrate(array $configurationData) : self
    {
        $configuration = new self();
        $configuration->styles = Styles::hydrate($configurationData['styles']);
        $configuration->branding = Branding::hydrate($configurationData['branding']);

        if (isset($configurationData['lastSavedView'])) {
            $configuration->lastSavedView = $configurationData['lastSavedView'];
        }

        return $configuration;
    }

    public function getBranding() : Branding
    {
        return $this->branding;
    }

    public function setBranding(Branding $branding) : void
    {
        $this->branding = $branding;
    }
}
