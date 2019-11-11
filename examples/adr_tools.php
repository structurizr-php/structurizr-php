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

use StructurizrPHP\StructurizrPHP\Client\Client;
use StructurizrPHP\StructurizrPHP\Client\Credentials;
use StructurizrPHP\StructurizrPHP\Client\Infrastructure\Http\SymfonyRequestFactory;
use StructurizrPHP\StructurizrPHP\Client\UrlMap;
use StructurizrPHP\StructurizrPHP\Core\Model\Tags;
use StructurizrPHP\StructurizrPHP\Core\View\Configuration\Shape;
use StructurizrPHP\StructurizrPHP\Core\Workspace;
use StructurizrPHP\Tests\StructurizrPHP\Tests\Unit\Core\Documentation\AdrToolsImporter;
use Symfony\Component\HttpClient\Psr18Client;

require __DIR__ . '/../vendor/autoload.php';

const FILE_SYSTEM_TAG = 'File System';
$workspace = new Workspace((string)\getenv('STRUCTURIZR_WORKSPACE_ID'), 'adr-tools', 'A description of the adr-tools command line utility.');
$model = $workspace->getModel();

$user = $model->addPerson('User', 'Somebody on a software development team.');
$adrTools = $model->addSoftwareSystem('adr-tools', 'A command-line tool for working with Architecture Decision Records (ADRs).');
$adrTools->setUrl('https://github.com/npryce/adr-tools');

$adrShellScripts = $adrTools->addContainer('adr', 'A command-line tool for working with Architecture Decision Records (ADRs).', 'Shell Scripts');
$adrShellScripts->setUrl('https://github.com/npryce/adr-tools/tree/master/src');
$fileSystem = $adrTools->addContainer('File System', 'Stores ADRs, templates, etc.', 'File System');
$fileSystem->addTags(FILE_SYSTEM_TAG);
$user->uses($adrShellScripts, 'Manages ADRs using');
$adrShellScripts->uses($fileSystem, 'Reads from and writes to');
$model->addImplicitRelationships();

$views = $workspace->getViews();
$contextView = $views->createSystemContextView($adrTools, 'SystemContext', 'The system context diagram for adr-tools.');
$contextView->addAllElements();

$containerView = $views->createContainerView($adrTools, 'Containers', 'The container diagram for adr-tools.');
$containerView->addAllElements();

$adrDirectory = __DIR__ . '/documentation/adr';

$adrToolsImporter = new AdrToolsImporter($workspace, $adrDirectory);
$adrToolsImporter->importArchitectureDecisionRecords($adrTools);

$styles = $views->getConfiguration()->getStyles();
$styles->addElementStyle(Tags::ELEMENT)->shape(Shape::roundedBox())->color('#ffffff');
$styles->addElementStyle(Tags::SOFTWARE_SYSTEM)->background('#18ADAD')->color('#ffffff');
$styles->addElementStyle(Tags::PERSON)->shape(Shape::person())->background('#008282')->color('#ffffff');
$styles->addElementStyle(Tags::CONTAINER)->background('#6DBFBF');
$styles->addElementStyle(FILE_SYSTEM_TAG)->shape(Shape::folder());


$client = new Client(
    new Credentials((string)\getenv('STRUCTURIZR_API_KEY'), (string)\getenv('STRUCTURIZR_API_SECRET')),
    new UrlMap('https://api.structurizr.com'),
    new Psr18Client(),
    new SymfonyRequestFactory()
);
$client->setMergeRemote(false);
$client->put($workspace);
