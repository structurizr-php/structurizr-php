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

namespace StructurizrPHP\Tests\StructurizrPHP\Tests\Unit\Core\View;

use PHPUnit\Framework\TestCase;
use StructurizrPHP\StructurizrPHP\Core\Util\ImageUtils;
use StructurizrPHP\StructurizrPHP\Core\View\Branding;

final class BrandingTest extends TestCase
{
    public function test_hydrating_empty_branding() : void
    {
        $branding = new Branding();

        $this->assertEquals($branding, Branding::hydrate($branding->toArray()));
    }

    public function test_hydrating_branding_with_logo() : void
    {
        $branding = new Branding();
        $branding->setLogo(ImageUtils::getImageAsDataUri(__DIR__ . '/assets/black-pixel.png'));
        $this->assertEquals($branding, Branding::hydrate($branding->toArray()));
    }
}
