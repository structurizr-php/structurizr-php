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
use StructurizrPHP\Core\Model\Tags;
use StructurizrPHP\Core\View\Configuration\Shape;
use StructurizrPHP\Core\Workspace;
use Symfony\Component\HttpClient\Psr18Client;

require __DIR__ . '/../vendor/autoload.php';

const DATABASE_TAG = 'Database';

$workspace = new Workspace(
    $id = (string) \getenv('STRUCTURIZR_WORKSPACE_ID'),
    $name = 'Http Health Checks',
    $description = 'An example of how to use the HTTP-based health checks feature'
);

$structurizr = $workspace->getModel()->addSoftwareSystem('Structurizr', 'A publishing platform for software architecture diagrams and documentation based upon the C4 model.');
$webApplication = $structurizr->addContainer('structurizr.com', 'Provides all of the server-side functionality of Structurizr, serving static and dynamic content to users.', 'Java and Spring MVC');
$database = $structurizr->addContainer('Database', 'Stores information about users, workspaces, etc.', 'Relational Database Schema');
$database->addTags(DATABASE_TAG);
$webApplication->usesContainer($database, 'Reads from and writes to', 'JDBC');
$component = $webApplication->addComponent('System', 'Facade', 'System Facade');
$component->setTechnology('php');

$amazonWebServices = $workspace->getModel()->addDeploymentNode('Amazon Web Services', 'Live', 'us-east-1');
$pivotalWebServices = $amazonWebServices->addDeploymentNode('Pivotal Web Services', 'Live', 'Platform as a Service provider.', 'Cloud Foundry');
$liveWebApplication = $pivotalWebServices->addDeploymentNode('www.structurizr.com', 'Live', 'An open source Java EE web server.', 'Apache Tomcat')
    ->add($webApplication);
$liveDatabaseInstance = $amazonWebServices->addDeploymentNode('Amazon RDS', 'Live', 'Database as a Service provider.', 'MySQL')
    ->add($database);

// add health checks to the container instances, which return a simple HTTP 200 to say everything is okay
$liveWebApplication->addHealthCheck('Web Application is running', 'https://www.structurizr.com/health');
$liveDatabaseInstance->addHealthCheck('Database is accessible from Web Application', 'https://www.structurizr.com/health/database');

// the pass/fail status from the health checks is used to supplement any deployment views that include the container instances that have health checks defined
$deploymentView = $workspace->getViews()->createDeploymentView($structurizr, 'Deployment', 'A deployment diagram showing the live environment.');
$deploymentView->setEnvironment('Live');
$deploymentView->addAllDeploymentNodes();

$workspace->getViews()->getConfiguration()->getStyles()->addElementStyle(Tags::ELEMENT)->color('#ffffff');
$workspace->getViews()->getConfiguration()->getStyles()->addElementStyle(DATABASE_TAG)->shape(Shape::cylinder());

$client = new Client(
    new Credentials((string) \getenv('STRUCTURIZR_API_KEY'), (string) \getenv('STRUCTURIZR_API_SECRET')),
    new UrlMap('https://api.structurizr.com'),
    new Psr18Client(),
    new SymfonyRequestFactory(),
    // Logger can be replaced with new NullLogger()
    (new Logger('structurizr'))->pushHandler(new StreamHandler(__DIR__ . '/var/logs/' . \basename(__FILE__) . '.log', Logger::DEBUG))
);

$client->put($workspace);
