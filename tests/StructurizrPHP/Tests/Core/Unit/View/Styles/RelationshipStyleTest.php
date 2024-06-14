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
use StructurizrPHP\Core\View\Configuration\RelationshipStyle;
use StructurizrPHP\Core\View\Routing;

final class RelationshipStyleTest extends TestCase
{
    public function test_hydrating_relationship_style() : void
    {
        $relationshipStyle = new RelationshipStyle('tag');

        $this->assertEquals($relationshipStyle, RelationshipStyle::hydrate($relationshipStyle->toArray()));
    }

    public function test_hydrating_relationship_style_with_all_properties() : void
    {
        $relationshipStyle = new RelationshipStyle('tag');

        $relationshipStyle
            ->thickness(\random_int(1, 100))
            ->fontSize(\random_int(1, 100))
            ->width(\random_int(1, 100))
            ->opacity(\random_int(0, 100))
            ->setPosition(\random_int(0, 100))
            ->color('#ffffff')
            ->dashed(true)
            ->setRouting(Routing::direct());

        $this->assertEquals($relationshipStyle, RelationshipStyle::hydrate($relationshipStyle->toArray()));
    }
}
