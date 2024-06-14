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
use StructurizrPHP\Core\Model\Relationship\InteractionStyle;
use StructurizrPHP\Core\Model\Tags;
use StructurizrPHP\Core\View\Configuration\Shape;
use StructurizrPHP\Core\Workspace;
use Symfony\Component\HttpClient\Psr18Client;

require __DIR__ . '/../vendor/autoload.php';

$workspace = new Workspace(
    $id = (string) \getenv('STRUCTURIZR_WORKSPACE_ID'),
    $name = 'Internal / External system communication',
    $description = 'This is a model of software system by structurizr-php/structurizr-php'
);
$workspace->getModel()->setEnterprise(new Enterprise('Structurizr PHP'));
$person = $workspace->getModel()->addPerson(
    $name = 'User',
    $description = 'A user of my software system.',
    Location::internal()
);
$internalSystem = $workspace->getModel()->addSoftwareSystem(
    $name = 'Internal System',
    $description = 'Internal software system.',
    Location::internal()
);
$externalSystem = $workspace->getModel()->addSoftwareSystem(
    $name = 'External System',
    $description = 'External software system.',
    Location::external()
);

$internalSystemContainer = $internalSystem->addContainer('Internal Container', 'Notifies external system', 'PHP');
$person->usesContainer($internalSystemContainer, 'Uses');
$person->usesContainer($internalSystemContainer, 'Manages');
$internalSystemContainer->usesSoftwareSystem($externalSystem, 'Notifies', 'Https', InteractionStyle::asynchronous());
$externalSystem->delivers($person, 'Notifies', 'Text Message');

$containerView = $workspace->getViews()->createContainerView($internalSystem, 'container', 'container');
$containerView->addSoftwareSystem($externalSystem);
$containerView->addContainer($internalSystemContainer);
$containerView->addPerson($person);

$styles = $workspace->getViews()->getConfiguration()->getStyles();

$styles->addElementStyle(Tags::CONTAINER)->background('#1168bd')->color('#ffffff');
$styles->addElementStyle(Tags::PERSON)->background('#08427b')->color('#ffffff')->shape(Shape::person());
$styles->addRelationshipStyle(Tags::RELATIONSHIP)->color('#ff0000');

$client = new Client(
    new Credentials((string) \getenv('STRUCTURIZR_API_KEY'), (string) \getenv('STRUCTURIZR_API_SECRET')),
    new UrlMap('https://api.structurizr.com'),
    new Psr18Client(),
    new SymfonyRequestFactory(),
    // Logger can be replaced with new NullLogger()
    (new Logger('structurizr'))->pushHandler(new StreamHandler(__DIR__ . '/var/logs/' . \basename(__FILE__) . '.log', Logger::DEBUG))
);
$client->put($workspace);
