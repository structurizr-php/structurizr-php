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
use StructurizrPHP\StructurizrPHP\Core\Model\Model;
use StructurizrPHP\StructurizrPHP\Core\View\DynamicView;
use StructurizrPHP\StructurizrPHP\Core\View\ViewSet;

final class DynamicViewTest extends TestCase
{
    public function test_hydrating_dynamic_view() : void
    {
        $viewSet = new ViewSet(new Model());
        $system = $viewSet->getModel()->addSoftwareSystem('system');

        $view = $viewSet->createDynamicView($system, 'key', 'description');

        $this->assertEquals($view, DynamicView::hydrate($view->toArray(), $viewSet));
    }

    public function test_hydrating_dynamic_view_with_relationships() : void
    {
        $viewSet = new ViewSet(new Model());
        $system = $viewSet->getModel()->addSoftwareSystem('system');
        $container = $viewSet->getModel()->addContainer($system, 'container', 'test', 'php');
        $person = $viewSet->getModel()->addPerson('person');

        $person->usesContainer($container);

        $view = $viewSet->createDynamicView($system, 'key', 'description');
        $view->add($person, 'is using', $container);

        $this->assertEquals($view, DynamicView::hydrate($view->toArray(), $viewSet));
    }
}
