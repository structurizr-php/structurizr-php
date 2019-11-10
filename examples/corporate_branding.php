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

use StructurizrPHP\StructurizrPHP\Core\Documentation\Format;
use StructurizrPHP\StructurizrPHP\Core\Documentation\StructurizrDocumentationTemplate;
use StructurizrPHP\StructurizrPHP\Core\Model\Tags;
use StructurizrPHP\StructurizrPHP\Core\Util\ImageUtils;
use StructurizrPHP\StructurizrPHP\Core\View\Configuration\Shape;
use StructurizrPHP\StructurizrPHP\Core\Workspace;
use StructurizrPHP\StructurizrPHP\Infrastructure\Http\SymfonyRequestFactory;
use StructurizrPHP\StructurizrPHP\SDK\Client;
use StructurizrPHP\StructurizrPHP\SDK\Credentials;
use StructurizrPHP\StructurizrPHP\SDK\UrlMap;
use Symfony\Component\HttpClient\Psr18Client;

require __DIR__ . '/../vendor/autoload.php';

$workspace = new Workspace((string)\getenv('STRUCTURIZR_WORKSPACE_ID'), 'Corporate Branding', 'This is a model of my software system.');
$model = $workspace->getModel();

$user = $model->addPerson('User', 'A user of my software system.');
$softwareSystem = $model->addSoftwareSystem('Software System', 'My software system.');
$user->uses($softwareSystem, 'Uses');

$views = $workspace->getViews();
$contextView = $views->createSystemContextView($softwareSystem, 'SystemContext', 'An example of a System Context diagram.');
$contextView->addAllSoftwareSystems();
$contextView->addAllPeople();

$styles = $views->getConfiguration()->getStyles();
$styles->addElementStyle(Tags::PERSON)->shape(Shape::person());

$template = new StructurizrDocumentationTemplate($workspace);
$template->addContextSection($softwareSystem, Format::markdown(), 'Here is some context about the software system...\n\n![](embed:SystemContext)');

$branding = $views->getConfiguration()->getBranding();
$branding->setLogo(ImageUtils::getImageAsDataUri(__DIR__.'/documentation/logo.png'));


$client = new Client(
    new Credentials((string) \getenv('STRUCTURIZR_API_KEY'), (string) \getenv('STRUCTURIZR_API_SECRET')),
    new UrlMap('https://api.structurizr.com'),
    new Psr18Client(),
    new SymfonyRequestFactory()
);
$client->put($workspace);
