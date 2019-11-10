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

namespace StructurizrPHP\Tests\StructurizrPHP\Tests\Unit\Core\Model;

use PHPUnit\Framework\TestCase;
use StructurizrPHP\StructurizrPHP\Core\Model\ContainerInstance;
use StructurizrPHP\StructurizrPHP\Core\Model\Model;

final class ContainerInstanceTest extends TestCase
{
    public function test_hydrate_container_instance() : void
    {
        $model = new Model();
        $softwareSystem = $model->addSoftwareSystem('system');
        $container = $model->addContainer($softwareSystem, 'container', 'test', 'php');
        $deploymentNode = $model->addDeploymentNode('node');
        $instance = $model->addContainerInstance($deploymentNode, $container);

        $newModel = new Model();
        $softwareSystem = $newModel->addSoftwareSystem('system');
        $container = $newModel->addContainer($softwareSystem, 'container', 'test', 'php');
        $deploymentNode = $newModel->addDeploymentNode('node');

        $this->assertEquals($instance, ContainerInstance::hydrate($instance->toArray(), $newModel));
    }
}
