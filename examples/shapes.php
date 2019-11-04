<?php

/*
 * This file is part of the Structurizr for PHP.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use StructurizrPHP\StructurizrPHP\Core\Model\Tags;
use StructurizrPHP\StructurizrPHP\Core\View\Configuration\Shape;
use StructurizrPHP\StructurizrPHP\Core\View\PaperSize;
use StructurizrPHP\StructurizrPHP\Core\Workspace;
use StructurizrPHP\StructurizrPHP\Infrastructure\Http\SymfonyRequestFactory;
use StructurizrPHP\StructurizrPHP\SDK\Client;
use StructurizrPHP\StructurizrPHP\SDK\Credentials;
use StructurizrPHP\StructurizrPHP\SDK\UrlMap;
use StructurizrPHP\StructurizrPHP\SDK\WorkspaceHydrator;
use Symfony\Component\HttpClient\Psr18Client;

require __DIR__ . '/../vendor/autoload.php';

$client = new Client(
    new Credentials((string) \getenv('STRUCTURIZR_API_KEY'), (string) \getenv('STRUCTURIZR_API_SECRET')),
    new UrlMap('https://api.structurizr.com'),
    new Psr18Client(),
    new SymfonyRequestFactory(),
);

$workspace = new Workspace(
    $id = (string)\getenv('STRUCTURIZR_WORKSPACE_ID'),
    "Shapes",
    "An example of all shapes available in Structurizr."
);

$model = $workspace->model();

$model->addSoftwareSystem("Box", "Description")->addTags("Box");
$model->addSoftwareSystem("RoundedBox", "Description")->addTags("RoundedBox");
$model->addSoftwareSystem("Ellipse", "Description")->addTags("Ellipse");
$model->addSoftwareSystem("Circle", "Description")->addTags("Circle");
$model->addSoftwareSystem("Hexagon", "Description")->addTags("Hexagon");
$model->addSoftwareSystem("Cylinder", "Description")->addTags("Cylinder");
$model->addSoftwareSystem("WebBrowser", "Description")->addTags("Web Browser");
$model->addSoftwareSystem("Mobile Device Portrait", "Description")->addTags("Mobile Device Portrait");
$model->addSoftwareSystem("Mobile Device Landscape", "Description")->addTags("Mobile Device Landscape");
$model->addSoftwareSystem("Pipe", "Description")->addTags("Pipe");
$model->addSoftwareSystem("Folder", "Description")->addTags("Folder");
$model->addSoftwareSystem("Robot", "Description")->addTags("Robot");
$model->addPerson("Person", "Description")->addTags("Person");

$views = $workspace->viewSet();

$view = $views->createSystemLandscapeView("shapes", "An example of all shapes available in Structurizr.");
$view->addAllElements();
$view->setPaperSize(PaperSize::A5_Landscape());

$styles = $views->getConfiguration()->getStyles();

$styles->addElementStyle(Tags::ELEMENT)->color("#ffffff")->background("#438dd5")->fontSize(34)->width(650)->height(400);
$styles->addElementStyle("Box")->shape(Shape::box());
$styles->addElementStyle("RoundedBox")->shape(Shape::roundedBox());
$styles->addElementStyle("Ellipse")->shape(Shape::ellipse());
$styles->addElementStyle("Circle")->shape(Shape::circle());
$styles->addElementStyle("Cylinder")->shape(Shape::cylinder());
$styles->addElementStyle("Web Browser")->shape(Shape::webBrowser());
$styles->addElementStyle("Mobile Device Portrait")->shape(Shape::mobileDevicePortrait())->width(400)->height(650);
$styles->addElementStyle("Mobile Device Landscape")->shape(Shape::mobileDeviceLandscape());
$styles->addElementStyle("Pipe")->shape(Shape::pipe());
$styles->addElementStyle("Folder")->shape(Shape::folder());
$styles->addElementStyle("Hexagon")->shape(Shape::hexagon());
$styles->addElementStyle("Robot")->shape(Shape::robot())->width(550);
$styles->addElementStyle("Person")->shape(Shape::person())->width(550);

$client->put($workspace);
