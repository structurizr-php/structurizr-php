<?php

/*
 * This file is part of the Structurizr SDK for PHP.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use StructurizrPHP\StructurizrPHP\Core\Model\Enterprise;
use StructurizrPHP\StructurizrPHP\Core\Model\Location;
use StructurizrPHP\StructurizrPHP\Core\Model\Properties;
use StructurizrPHP\StructurizrPHP\Core\Model\Property;
use StructurizrPHP\StructurizrPHP\Core\Model\Tags;
use StructurizrPHP\StructurizrPHP\Core\View\Configuration\Shape;
use StructurizrPHP\StructurizrPHP\Core\Workspace;
use StructurizrPHP\StructurizrPHP\Infrastructure\Http\SymfonyRequestFactory;
use StructurizrPHP\StructurizrPHP\SDK\Client;
use StructurizrPHP\StructurizrPHP\SDK\Credentials;
use StructurizrPHP\StructurizrPHP\SDK\UrlMap;
use Symfony\Component\HttpClient\Psr18Client;

require __DIR__ . '/../vendor/autoload.php';

$client = new Client(
    new Credentials((string) \getenv('STRUCTURIZR_API_KEY'), (string) \getenv('STRUCTURIZR_API_SECRET')),
    new UrlMap('https://api.structurizr.com'),
    new Psr18Client(),
    new SymfonyRequestFactory()
);

$workspace = new Workspace(
    $id = (string)\getenv('STRUCTURIZR_WORKSPACE_ID'),
    $name = 'Test Workspace',
    $description = 'This is just a test workspace created by structurizr-php/sdk'
);
$workspace->model()->setEnterprise(new Enterprise('Norbert Enterprise'));

$system1 = $workspace->model()->addSoftwareSystem(
    Location::internal(),
    $name = 'Software System 1',
    $description = 'First Software System'
);
$authorizedUser = $workspace->model()->addPerson(
    Location::internal(),
    $name = 'Authorized User',
    $description = '...'
);

$authorizedUser->setProperties(new Properties(new Property('domain', 'example.com')));
$authorizedUser->usesSoftwareSystem($system1, 'Uses', 'Http');

$system1View = $workspace->viewSet()->createSystemContextView($system1, 'System 1 view', 'system01', 'System 1 view description');
$system1View->addAllElements();

$workspace->viewSet()->configuration()->styles()->addElementStyle(Tags::PERSON)->setShape(Shape::person());

$client->put($workspace);
