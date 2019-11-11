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

namespace StructurizrPHP\Tests\StructurizrPHP\Tests\Core\Unit\View;

use PHPUnit\Framework\TestCase;
use StructurizrPHP\StructurizrPHP\Core\Model\Model;
use StructurizrPHP\StructurizrPHP\Core\View\ViewSet;

final class ViewSetTest extends TestCase
{
    public function test_hydrating_view_set() : void
    {
        $viewSet = new ViewSet($model = new Model());

        $this->assertEquals($viewSet, ViewSet::hydrate($viewSet->toArray(), $model));
    }

    public function test_hydrating_view_set_with_views() : void
    {
        $viewSet = new ViewSet($model = new Model());
        $softwareSystem = $model->addSoftwareSystem('software');

        $viewSet->createSystemLandscapeView('landscape', 'Landscape View');
        $viewSet->createSystemContextView($softwareSystem, 'context', 'Context View');
        $viewSet->createDynamicView($softwareSystem, 'dynamic', 'Dynamic View');
        $viewSet->createDeploymentView($softwareSystem, 'deployment', 'Deployment View');

        $this->assertEquals($viewSet, ViewSet::hydrate($viewSet->toArray(), $model));
    }
}
