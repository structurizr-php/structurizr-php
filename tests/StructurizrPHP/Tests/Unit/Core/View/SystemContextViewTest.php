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
use StructurizrPHP\StructurizrPHP\Core\View\SystemContextView;
use StructurizrPHP\StructurizrPHP\Core\View\ViewSet;

final class SystemContextViewTest extends TestCase
{
    public function test_hydrating_system_landscape_view() : void
    {
        $viewSet = new ViewSet(new Model());
        $softwareSystem = $viewSet->getModel()->addSoftwareSystem('name', 'description');
        $view = $viewSet->createSystemContextView($softwareSystem, 'key', 'description');

        $this->assertEquals($view, SystemContextView::hydrate($view->toArray(), $viewSet));
    }

    public function test_hydrating_system_landscape_view_with_automatic_layout() : void
    {
        $viewSet = new ViewSet(new Model());
        $softwareSystem = $viewSet->getModel()->addSoftwareSystem('name', 'description');
        $view = $viewSet->createSystemContextView($softwareSystem, 'key', 'description');
        $view->setAutomaticLayout(true);

        $this->assertEquals($view, SystemContextView::hydrate($view->toArray(), $viewSet));
    }

    public function test_hydrating_system_landscape_view_with_added_people() : void
    {
        $viewSet = new ViewSet(new Model());
        $softwareSystem = $viewSet->getModel()->addSoftwareSystem('name', 'description');
        $person = $viewSet->getModel()->addPerson('name', 'description');
        $person->usesSoftwareSystem($softwareSystem, 'description', 'technology');

        $view = $viewSet->createSystemContextView($softwareSystem, 'key', 'description');
        $view->addAllElements();

        $this->assertEquals($view, SystemContextView::hydrate($view->toArray(), $viewSet));
    }
}
