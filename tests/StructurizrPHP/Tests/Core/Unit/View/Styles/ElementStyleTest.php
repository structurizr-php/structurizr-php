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
use StructurizrPHP\Core\View\Configuration\ElementStyle;
use StructurizrPHP\Core\View\Configuration\Shape;

final class ElementStyleTest extends TestCase
{
    public function test_hydrating_element_style() : void
    {
        $elementStyle = new ElementStyle('tag');

        $this->assertEquals($elementStyle, ElementStyle::hydrate($elementStyle->toArray()));
    }

    public function test_hydrating_element_style_with_all_properties() : void
    {
        $elementStyle = new ElementStyle('tag');

        $elementStyle->shape(Shape::robot())->width(100)->height(150)
            ->setBorder(Border::dashed())->background('#ffffff')->color('#ffffff')->iconUrl('http://placeholder.com');

        $elementStyle->setDescription('description');
        $elementStyle->setMetadata(true);

        $this->assertEquals($elementStyle, ElementStyle::hydrate($elementStyle->toArray()));
    }
}
