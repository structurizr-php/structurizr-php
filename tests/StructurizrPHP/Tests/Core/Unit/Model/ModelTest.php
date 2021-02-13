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

namespace StructurizrPHP\Tests\Core\Unit\Model;

use StructurizrPHP\Core\Model\Model;
use StructurizrPHP\Tests\Core\Unit\AbstractWorkspaceTestBase;

final class ModelTest extends AbstractWorkspaceTestBase
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

    public function test_add_implicit_relationships_when_source_and_destination_are_components_in_different_software_systems() : void
    {
        $a = $this->model->addSoftwareSystem('A', '');
        $aa = $a->addContainer('AA', '', '');
        $aaa = $aa->addComponent('AAA', '', '');

        $b = $this->model->addSoftwareSystem('B', '');
        $bb = $b->addContainer('BB', '', '');
        $bbb = $bb->addComponent('BBB', '', '');

        $aaa->usesComponent($bbb, 'Uses');
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertTrue($aaa->hasEfferentRelationshipWith($bbb));

        // AAA->BBB implies AAA->BB AAA->B AA->BBB AA->BB AA->B A->BBB A->BB A->B
        $implicitRelationships = $this->model->addImplicitRelationships();
        $this->assertCount(9, $this->model->getRelationships());
        $this->assertCount(8, $implicitRelationships);
        $this->assertTrue($aaa->hasEfferentRelationshipWith($bb));
        $this->assertTrue($aa->hasEfferentRelationshipWith($bbb));
        $this->assertTrue($aa->hasEfferentRelationshipWith($bb));
        $this->assertTrue($aaa->hasEfferentRelationshipWith($b));
        $this->assertTrue($a->hasEfferentRelationshipWith($bbb));
        $this->assertTrue($aa->hasEfferentRelationshipWith($b));
        $this->assertTrue($a->hasEfferentRelationshipWith($bb));
        $this->assertTrue($a->hasEfferentRelationshipWith($b));
    }

    public function test_add_implicit_relationships_when_source_is_a_component_and_destination_is_a_container_in_a_different_software_system() : void
    {
        $a = $this->model->addSoftwareSystem('A', '');
        $aa = $a->addContainer('AA', '', '');
        $aaa = $aa->addComponent('AAA', '', '');

        $b = $this->model->addSoftwareSystem('B', '');
        $bb = $b->addContainer('BB', '', '');

        $aaa->usesContainer($bb, 'Uses');
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertTrue($aaa->hasEfferentRelationshipWith($bb));

        // AAA->BB implies AAA->B AA->BB AA->B A->BB A->B
        $implicitRelationships = $this->model->addImplicitRelationships();
        $this->assertCount(6, $this->model->getRelationships());
        $this->assertCount(5, $implicitRelationships);
        $this->assertTrue($aa->hasEfferentRelationshipWith($bb));
        $this->assertTrue($aaa->hasEfferentRelationshipWith($b));
        $this->assertTrue($aa->hasEfferentRelationshipWith($b));
        $this->assertTrue($a->hasEfferentRelationshipWith($bb));
        $this->assertTrue($a->hasEfferentRelationshipWith($b));
    }

    public function test_add_implicit_relationships_when_source_is_a_component_and_destination_is_a_different_software_system() : void
    {
        $a = $this->model->addSoftwareSystem('A', '');
        $aa = $a->addContainer('AA', '', '');
        $aaa = $aa->addComponent('AAA', '', '');

        $b = $this->model->addSoftwareSystem('B', '');

        $aaa->usesSoftwareSystem($b, 'Uses');
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertTrue($aaa->hasEfferentRelationshipWith($b));

        // AAA->B implies AA->B A->B
        $implicitRelationships = $this->model->addImplicitRelationships();
        $this->assertCount(3, $this->model->getRelationships());
        $this->assertCount(2, $implicitRelationships);
        $this->assertTrue($aa->hasEfferentRelationshipWith($b));
        $this->assertTrue($a->hasEfferentRelationshipWith($b));
    }

    public function test_add_implicit_relationships_when_source_is_a_container_and_destination_is_a_component_in_a_different_software_system() : void
    {
        $a = $this->model->addSoftwareSystem('A', '');
        $aa = $a->addContainer('AA', '', '');

        $b = $this->model->addSoftwareSystem('B', '');
        $bb = $b->addContainer('BB', '', '');
        $bbb = $bb->addComponent('BBB', '', '');

        $aa->usesComponent($bbb, 'Uses');
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertTrue($aa->hasEfferentRelationshipWith($bbb));

        // AA->BBB implies AA->BB AA->B A->BBB A->BB A->B
        $implicitRelationships = $this->model->addImplicitRelationships();
        $this->assertCount(6, $this->model->getRelationships());
        $this->assertCount(5, $implicitRelationships);
        $this->assertTrue($aa->hasEfferentRelationshipWith($bb));
        $this->assertTrue($aa->hasEfferentRelationshipWith($b));
        $this->assertTrue($a->hasEfferentRelationshipWith($bbb));
        $this->assertTrue($a->hasEfferentRelationshipWith($bb));
        $this->assertTrue($a->hasEfferentRelationshipWith($b));
    }

    public function test_add_implicit_relationships_when_source_and_destination_are_containers_in_different_software_systems() : void
    {
        $a = $this->model->addSoftwareSystem('A', '');
        $aa = $a->addContainer('AA', '', '');

        $b = $this->model->addSoftwareSystem('B', '');
        $bb = $b->addContainer('BB', '', '');

        $aa->usesContainer($bb, 'Uses');
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertTrue($aa->hasEfferentRelationshipWith($bb));

        // AA->BB implies AA->B A->BB A->B
        $implicitRelationships = $this->model->addImplicitRelationships();
        $this->assertCount(4, $this->model->getRelationships());
        $this->assertCount(3, $implicitRelationships);
        $this->assertTrue($aa->hasEfferentRelationshipWith($b));
        $this->assertTrue($a->hasEfferentRelationshipWith($bb));
        $this->assertTrue($a->hasEfferentRelationshipWith($b));
    }

    public function test_add_implicit_relationships_when_source_is_a_container_and_destination_is_a_different_software_system() : void
    {
        $a = $this->model->addSoftwareSystem('A', '');
        $aa = $a->addContainer('AA', '', '');

        $b = $this->model->addSoftwareSystem('B', '');

        $aa->usesSoftwareSystem($b, 'Uses');
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertTrue($aa->hasEfferentRelationshipWith($b));

        // AA->B implies A->B
        $implicitRelationships = $this->model->addImplicitRelationships();
        $this->assertCount(2, $this->model->getRelationships());
        $this->assertCount(1, $implicitRelationships);
        $this->assertTrue($a->hasEfferentRelationshipWith($b));
    }

    public function test_add_implicit_relationships_when_source_is_a_software_system_and_destination_is_a_component_in_a_different_software_system() : void
    {
        $a = $this->model->addSoftwareSystem('A', '');

        $b = $this->model->addSoftwareSystem('B', '');
        $bb = $b->addContainer('BB', '', '');
        $bbb = $bb->addComponent('BBB', '', '');

        $a->usesComponent($bbb, 'Uses');
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertTrue($a->hasEfferentRelationshipWith($bbb));

        // A->BBB implies A->BB A->B
        $implicitRelationships = $this->model->addImplicitRelationships();
        $this->assertCount(3, $this->model->getRelationships());
        $this->assertCount(2, $implicitRelationships);
        $this->assertTrue($a->hasEfferentRelationshipWith($bb));
        $this->assertTrue($a->hasEfferentRelationshipWith($b));
    }

    public function test_add_implicit_relationships_when_source_is_a_software_system_and_destination_is_a_container_in_a_different_software_system() : void
    {
        $a = $this->model->addSoftwareSystem('A', '');

        $b = $this->model->addSoftwareSystem('B', '');
        $bb = $b->addContainer('BB', '', '');

        $a->usesContainer($bb, 'Uses');
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertTrue($a->hasEfferentRelationshipWith($bb));

        // A->BB implies A->B
        $implicitRelationships = $this->model->addImplicitRelationships();
        $this->assertCount(2, $this->model->getRelationships());
        $this->assertCount(1, $implicitRelationships);
        $this->assertTrue($a->hasEfferentRelationshipWith($b));
    }

    public function test_add_implicit_relationships_when_source_and_destination_are_different_software_systems() : void
    {
        $a = $this->model->addSoftwareSystem('A', '');

        $b = $this->model->addSoftwareSystem('B', '');

        $a->usesSoftwareSystem($b, 'Uses');
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertTrue($a->hasEfferentRelationshipWith($b));

        $implicitRelationships = $this->model->addImplicitRelationships();
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertCount(0, $implicitRelationships);
    }

    public function test_add_implicit_relationships_when_source_and_destination_are_components_in_the_same_container() : void
    {
        $a = $this->model->addSoftwareSystem('A', '');
        $aa = $a->addContainer('AA', '', '');
        $aaa1 = $aa->addComponent('AAA1', '', '');
        $aaa2 = $aa->addComponent('AAA2', '', '');

        $aaa1->usesComponent($aaa2, 'Uses');
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertTrue($aaa1->hasEfferentRelationshipWith($aaa2));

        $implicitRelationships = $this->model->addImplicitRelationships();
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertCount(0, $implicitRelationships);
    }

    public function test_add_implicit_relationships_when_source_and_destination_are_containers_in_the_same_container() : void
    {
        $a = $this->model->addSoftwareSystem('A', '');
        $aa1 = $a->addContainer('AA1', '', '');
        $aa2 = $a->addContainer('AA2', '', '');

        $aa1->usesContainer($aa2, 'Uses');
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertTrue($aa1->hasEfferentRelationshipWith($aa2));

        $implicitRelationships = $this->model->addImplicitRelationships();
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertCount(0, $implicitRelationships);
    }

    public function test_add_implicit_relationships_when_source_and_destination_are_components_in_the_different_containers_in_the_same_software_system() : void
    {
        $a = $this->model->addSoftwareSystem('A', '');
        $aa1 = $a->addContainer('AA1', '', '');
        $aa2 = $a->addContainer('AA2', '', '');
        $aaa1 = $aa1->addComponent('AAA1', '', '');
        $aaa2 = $aa2->addComponent('AAA2', '', '');

        $aaa1->usesComponent($aaa2, 'Uses');
        $this->assertCount(1, $this->model->getRelationships());
        $this->assertTrue($aaa1->hasEfferentRelationshipWith($aaa2));

        // AAA1->AAA2 implies AAA1->AA2 AA1->AAA2 AA1->AA2
        $implicitRelationships = $this->model->addImplicitRelationships();
        $this->assertCount(4, $this->model->getRelationships());
        $this->assertCount(3, $implicitRelationships);
        $this->assertTrue($aaa1->hasEfferentRelationshipWith($aa2));
        $this->assertTrue($aa1->hasEfferentRelationshipWith($aaa2));
        $this->assertTrue($aa1->hasEfferentRelationshipWith($aa2));
    }
}
