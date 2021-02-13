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

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use StructurizrPHP\Client\Client;
use StructurizrPHP\Client\Credentials;
use StructurizrPHP\Client\Infrastructure\Http\SymfonyRequestFactory;
use StructurizrPHP\Client\UrlMap;
use StructurizrPHP\Core\Model\Enterprise;
use StructurizrPHP\Core\Model\Location;
use StructurizrPHP\Core\View\Configuration\Shape;
use StructurizrPHP\Core\Workspace;
use Symfony\Component\HttpClient\Psr18Client;

require __DIR__ . '/../vendor/autoload.php';

$workspace = new Workspace(
    $id = (string) \getenv('STRUCTURIZR_WORKSPACE_ID'),
    $name = 'Getting Started',
    $description = 'This is a model of my software system. by structurizr-php/structurizr-php'
);
$workspace->getModel()->setEnterprise(new Enterprise('Structurizr PHP'));
$softwareSystem = $workspace->getModel()->addSoftwareSystem(
    $name = 'Software System',
    $description = 'My software system.',
    Location::internal()
);

$appGateway = $softwareSystem->addContainer('Application Gateway', 'System Gateway', 'Azure AppGateway');

$php = $softwareSystem->addContainer('Backend', 'Handles requests', 'PHP');
$php->addTags('PHP');

$db = $softwareSystem->addContainer('Database', 'Stores data', 'PostgreSQL');
$db->addTags('DATABASE');

$vnet = $workspace->getModel()->addDeploymentNode('vnet01', 'prod', 'Virtual Network', 'vnet_prod_eus_01', 1);

$subnetDb = $vnet->addDeploymentNode('subnet03', 'prod', 'Database Subnet', 'subnet_db_01', 1);
$subnetDb->add($db);

$appSubnet = $vnet->addDeploymentNode('subnet02', 'prod', 'Application Subnet', 'subnet_app_01', 2);
$appSubnet->add($php);

$appGatewaySubnet = $vnet->addDeploymentNode('subnet01', 'prod', 'AppGateway Subnet', 'subnet_app_gw_01', 1);
$appGatewaySubnet->add($appGateway);

$appGatewayAppRelationship = $appGatewaySubnet->usesDeploymentNode($appSubnet, 'Send requests', 'port: 443');
$appDBRelationship = $appSubnet->usesDeploymentNode($subnetDb, 'Reads data', 'port: 5432');

$deploymentView = $workspace->getViews()->createDeploymentView($softwareSystem, 'deployment', 'An example of a System Deployment diagram.');
$deploymentView->addAllDeploymentNodes();
$deploymentView->addRelationship($appGatewayAppRelationship);
$deploymentView->addRelationship($appDBRelationship);

$workspace->getViews()->getConfiguration()->getStyles()->addElementStyle('DATABASE')
    ->shape(Shape::cylinder())
    ->background('#0064a5')
    ->color('#ffffff');

$workspace->getViews()->getConfiguration()->getStyles()->addElementStyle('PHP')
    ->shape(Shape::hexagon())
    ->background('#787CB5')
    ->color('#ffffff');

$client = new Client(
    new Credentials((string) \getenv('STRUCTURIZR_API_KEY'), (string) \getenv('STRUCTURIZR_API_SECRET')),
    new UrlMap('https://api.structurizr.com'),
    new Psr18Client(),
    new SymfonyRequestFactory(),
    // Logger can be replaced with new NullLogger()
    (new Logger('structurizr'))->pushHandler(new StreamHandler(__DIR__ . '/var/logs/' . \basename(__FILE__) . '.log', Logger::DEBUG))
);

$client->put($workspace);
