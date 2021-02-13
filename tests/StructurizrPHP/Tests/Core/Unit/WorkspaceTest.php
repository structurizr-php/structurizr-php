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

namespace StructurizrPHP\Tests\Core\Unit;

use PHPUnit\Framework\TestCase;
use StructurizrPHP\AdrTools\AdrToolsImporter;
use StructurizrPHP\Core\Documentation\Format;
use StructurizrPHP\Core\Documentation\StructurizrDocumentationTemplate;
use StructurizrPHP\Core\Model\Tags;
use StructurizrPHP\Core\Util\ImageUtils;
use StructurizrPHP\Core\View\Configuration\Shape;
use StructurizrPHP\Core\Workspace;

final class WorkspaceTest extends TestCase
{
    public function test_hydraing_workspace() : void
    {
        $workspace = new Workspace(
            '1',
            'name',
            'description'
        );

        $this->assertEquals($workspace, Workspace::hydrate($workspace->toArray('test')));
    }

    public function test_hydraing_with_relationships_and_views() : void
    {
        $workspace = new Workspace(
            '1',
            'name',
            'description'
        );

        $person = $workspace->getModel()->addPerson('person', 'test');
        $system = $workspace->getModel()->addSoftwareSystem('system2', 'test');

        $person->usesSoftwareSystem($system, 'uses', 'browser');

        $contextView = $workspace->getViews()->createSystemContextView($system, 'systemcontext', 'contextview');
        $contextView->addAllElements();

        $landscapeView = $workspace->getViews()->createSystemContextView($system, 'landscale', 'landscapeview');
        $landscapeView->addElement($person, true)->setY(100)->setX(200);

        $template = new StructurizrDocumentationTemplate($workspace);
        $template->addContextSection($system, Format::markdown(), 'Here is some context about the software system...\n\n![](embed:SystemContext)');

        $branding = $workspace->getViews()->getConfiguration()->getBranding();
        $branding->setLogo(ImageUtils::getImageAsDataUri(__DIR__ . '/../../../../../examples/documentation/logo.png'));

        $workspace->getViews()->getConfiguration()->getStyles()->addElementStyle(Tags::PERSON)
            ->shape(Shape::person());

        $this->assertEquals($workspace, Workspace::hydrate($workspace->toArray('test')));
    }

    public function test_hydraing_adr_case() : void
    {
        $FILE_SYSTEM_TAG = 'File System';
        $workspace = new Workspace(
            '1',
            'name',
            'description'
        );

        $model = $workspace->getModel();

        $user = $model->addPerson('User', 'Somebody on a software development team.');
        $adrTools = $model->addSoftwareSystem('adr-tools', 'A command-line tool for working with Architecture Decision Records (ADRs).');
        $adrTools->setUrl('https://github.com/npryce/adr-tools');

        $adrShellScripts = $adrTools->addContainer('adr', 'A command-line tool for working with Architecture Decision Records (ADRs).', 'Shell Scripts');
        $adrShellScripts->setUrl('https://github.com/npryce/adr-tools/tree/master/src');
        $fileSystem = $adrTools->addContainer('File System', 'Stores ADRs, templates, etc.', 'File System');
        $fileSystem->addTags($FILE_SYSTEM_TAG);
        $user->uses($adrShellScripts, 'Manages ADRs using');
        $adrShellScripts->uses($fileSystem, 'Reads from and writes to');
        $model->addImplicitRelationships();

        $views = $workspace->getViews();
        $contextView = $views->createSystemContextView($adrTools, 'SystemContext', 'The system context diagram for adr-tools.');
        $contextView->addAllElements();

        $containerView = $views->createContainerView($adrTools, 'Containers', 'The container diagram for adr-tools.');
        $containerView->addAllElements();

        $adrDirectory = __DIR__ . '/../../../../../examples/documentation/adr';

        $adrToolsImporter = new AdrToolsImporter($workspace, $adrDirectory);
        $adrToolsImporter->importArchitectureDecisionRecords($adrTools);

        $styles = $views->getConfiguration()->getStyles();
        $styles->addElementStyle(Tags::ELEMENT)->shape(Shape::roundedBox())->color('#ffffff');
        $styles->addElementStyle(Tags::SOFTWARE_SYSTEM)->background('#18ADAD')->color('#ffffff');
        $styles->addElementStyle(Tags::PERSON)->shape(Shape::person())->background('#008282')->color('#ffffff');
        $styles->addElementStyle(Tags::CONTAINER)->background('#6DBFBF');
        $styles->addElementStyle($FILE_SYSTEM_TAG)->shape(Shape::folder());

        $this->assertEquals($workspace, Workspace::hydrate($workspace->toArray('test')));
    }
}
