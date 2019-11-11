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

namespace StructurizrPHP\Tests\StructurizrPHP\Tests\Core\Unit\Model;

use PHPUnit\Framework\TestCase;
use StructurizrPHP\StructurizrPHP\Core\Model\Model;

final class ModelTest extends TestCase
{
    public function test_hydrating_empty_model() : void
    {
        $model = new Model();

        $this->assertEquals($model, Model::hydrate($model->toArray()));
        $this->assertTrue($model->isEmpty());
    }

    public function test_hydrating_model_with_people() : void
    {
        $model = new Model();

        $model->addPerson('test', 'test');

        $this->assertEquals($model, Model::hydrate($model->toArray()));
    }

    public function test_hydrating_model_with_software_system() : void
    {
        $model = new Model();

        $model->addSoftwareSystem('test', 'test');

        $this->assertEquals($model, Model::hydrate($model->toArray()));
    }

    public function test_hydrating_model_with_software_system_and_person() : void
    {
        $model = new Model();

        $model->addSoftwareSystem('test', 'test');
        $model->addPerson('test', 'test');

        $this->assertEquals($model, Model::hydrate($model->toArray()));
    }

    public function test_hydrating_model_with_software_system_and_person_relationship() : void
    {
        $model = new Model();

        $model->addPerson('test01', 'test01');
        $person = $model->addPerson('test02', 'test02');
        $softwareSystem = $model->addSoftwareSystem('test03', 'test03');
        $model->addSoftwareSystem('test04', 'test04');

        $person->usesSoftwareSystem($softwareSystem, 'description', 'technology');

        $this->assertEquals($model, Model::hydrate($model->toArray()));
    }

    public function test_hydrating_model_with_software_system_and_deployment_node() : void
    {
        $model = new Model();

        $model->addPerson('test01', 'test01');
        $model->addSoftwareSystem('test03', 'test03');
        $model->addDeploymentNode('vm01', 'prod');

        $model->addSoftwareSystem('test04', 'test04');

        $this->assertEquals($model, Model::hydrate($model->toArray()));
    }
}
