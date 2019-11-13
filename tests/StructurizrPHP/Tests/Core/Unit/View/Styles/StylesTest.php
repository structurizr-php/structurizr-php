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

namespace StructurizrPHP\Tests\Core\Unit\View\Styles;

use PHPUnit\Framework\TestCase;
use StructurizrPHP\Core\View\Configuration\Border;
use StructurizrPHP\Core\View\Configuration\Styles;

final class StylesTest extends TestCase
{
    public function test_hydrating_styles() : void
    {
        $styles = new Styles();

        $this->assertEquals($styles, Styles::hydrate($styles->toArray()));
    }

    public function test_hydrating_styles_with_element_styles() : void
    {
        $styles = new Styles();

        $style = $styles->addElementStyle('TEST');
        $style->width(100)->height(150)
            ->setBorder(Border::dashed())->background('#ffffff')->color('#ffffff')->iconUrl('http://placeholder.com');

        $style->setDescription('description');
        $style->setMetadata(true);

        $style = $styles->addElementStyle('TEST1');
        $style->width(300)->height(250)
            ->setBorder(Border::dashed())->background('#ffffff')->color('#ffffff')->iconUrl('http://placeholder.com');

        $style->setDescription('description');
        $style->setMetadata(true);

        $this->assertEquals($styles, Styles::hydrate($styles->toArray()));
    }
}
