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
use StructurizrPHP\StructurizrPHP\Core\View\DeploymentView;
use StructurizrPHP\StructurizrPHP\Core\View\ViewSet;

final class DeploymentViewTest extends TestCase
{
    public function test_hydrating_deployment_view()
    {
        $model = new Model();
        $viewSet = new ViewSet($model);
        $softwareSystem = $model->addSoftwareSystem('system');

        $view = new DeploymentView($softwareSystem, 'test', 'test', $viewSet);
        $view->setEnvironment('prod');

        $this->assertEquals($view, DeploymentView::hydrate($view->toArray(), $viewSet));
    }
}
