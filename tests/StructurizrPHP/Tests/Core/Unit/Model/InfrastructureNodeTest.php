<?php

declare(strict_types=1);

namespace StructurizrPHP\Tests\Core\Unit\Model;

use PHPUnit\Framework\TestCase;
use StructurizrPHP\Core\Model\InfrastructureNode;
use StructurizrPHP\Core\Model\Model;
use StructurizrPHP\Core\Model\Properties;
use StructurizrPHP\Core\Model\Property;
use StructurizrPHP\Core\Model\Tags;

final class InfrastructureNodeTest extends TestCase
{
    public function test_hydrating_infrastructure_node() : void
    {
        $model = new Model();
        $deploymentNode = $model->addDeploymentNode('node');

        $node = new InfrastructureNode($deploymentNode, '1', $model);
        $node->setDescription('test');
        $node->setTechnology('vm');
        $node->setProperties(new Properties(new Property('test', 'test')));
        $node->setTags(new Tags(Tags::RELATIONSHIP));

        $this->assertEquals($node, InfrastructureNode::hydrate($node->toArray(), $model));
    }
}
