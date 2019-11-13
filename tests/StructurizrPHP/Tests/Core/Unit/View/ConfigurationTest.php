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

namespace StructurizrPHP\Tests\Core\Unit\View;

use PHPUnit\Framework\TestCase;
use StructurizrPHP\Core\View\Branding;
use StructurizrPHP\Core\View\Configuration;

final class ConfigurationTest extends TestCase
{
    public function test_hydrating_configuration() : void
    {
        $configuration = new Configuration();

        $this->assertEquals($configuration, Configuration::hydrate($configuration->toArray()));
    }

    public function test_hydrating_configuration_with_branding() : void
    {
        $configuration = new Configuration();
        $configuration->setBranding(new Branding());
        $this->assertEquals($configuration, Configuration::hydrate($configuration->toArray()));
    }
}
