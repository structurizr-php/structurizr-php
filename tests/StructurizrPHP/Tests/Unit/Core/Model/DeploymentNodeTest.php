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
use StructurizrPHP\StructurizrPHP\Core\Model\DeploymentNode;
use StructurizrPHP\StructurizrPHP\Core\Model\Model;
use StructurizrPHP\StructurizrPHP\Core\Model\Properties;
use StructurizrPHP\StructurizrPHP\Core\Model\Property;
use StructurizrPHP\StructurizrPHP\Core\Model\Tags;

final class DeploymentNodeTest extends TestCase
{
    public function test_hydrating_deployment_node()
    {
        $node = new DeploymentNode('1', $model = new Model());
        $node->setEnvironment('prod');
        $node->setInstances(1);
        $node->setDescription('test');
        $node->setTechnology('vm');
        $node->setProperties(new Properties(new Property('test', 'test')));
        $node->setTags(new Tags(Tags::RELATIONSHIP));

        $this->assertEquals($node, DeploymentNode::hydrate($node->toArray(), $model));
    }

    public function test_hydrating_deployment_node_with_child()
    {
        $model = new Model();
        $node = $model->addDeploymentNode('vnet01', 'prod');
        $node->setInstances(1);
        $node->setDescription('test');
        $node->setTechnology('vnet');
        $node->setProperties(new Properties(new Property('test', 'test')));
        $node->setTags(new Tags(Tags::RELATIONSHIP));

        $this->assertEquals($node, DeploymentNode::hydrate($node->toArray(), $model));
    }

    public function test_hydrating_deployment_node_with_child_with_relationships()
    {
        $model = new Model();
        $deploymentNode = $model->addDeploymentNode('node_01');
        $nodeChild01 = $deploymentNode->addDeploymentNode('node_child_01');
        $nodeChild02 = $deploymentNode->addDeploymentNode('node_child_02');

        $nodeChild01->usesDeploymentNode($nodeChild02, 'for fun');

        $newNode = DeploymentNode::hydrate($deploymentNode->toArray(), $model);
        DeploymentNode::hydrateChildrenRelationships($newNode, $deploymentNode->toArray());
        $this->assertEquals($deploymentNode, $newNode);
    }
}
