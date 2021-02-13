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

namespace StructurizrPHP\Tests\Core\Unit\Documentation;

use StructurizrPHP\Core\Documentation\Documentation;
use StructurizrPHP\Core\Documentation\Format;
use StructurizrPHP\Core\Documentation\StructurizrDocumentationTemplate;
use StructurizrPHP\Core\Exception\AssertionException;
use StructurizrPHP\Core\Exception\InvalidArgumentException;
use StructurizrPHP\Core\Workspace;
use StructurizrPHP\Tests\Core\Unit\AbstractWorkspaceTestBase;

final class DocumentationTest extends AbstractWorkspaceTestBase
{
    public function test_empty_title_added() : void
    {
        $softwareSystem = $this->model->addSoftwareSystem('Software System', 'Description');
        $testcase = new Documentation($this->model);

        $this->expectException(AssertionException::class);

        $testcase->addSection($softwareSystem, '', Format::markdown(), 'test content');
    }

    public function test_empty_content_added() : void
    {
        $softwareSystem = $this->model->addSoftwareSystem('Software System', 'Description');
        $testcase = new Documentation($this->model);

        $this->expectException(AssertionException::class);

        $testcase->addSection($softwareSystem, 'test', Format::markdown(), '');
    }

    public function test_addSection_ThrowsAnException_WhenTheRelatedElementIsNotPresentInTheAssociatedModel() : void
    {
        try {
            $softwareSystem = $this->model->addSoftwareSystem('Software System', 'Description');
            $workspace = new Workspace('2', 'test', 'test');
            $workspace->getDocumentation()->addSection($softwareSystem, 'Title', Format::markdown(), 'Content');
            $this->fail();
        } catch (InvalidArgumentException $iae) {
            $this->assertEquals(
                'The element named Software System does not exist in the model associated with this documentation.',
                $iae->getMessage()
            );
        }
    }

    public function test_hydrating_empty_documentation() : void
    {
        $documentation = new Documentation($this->model);

        $this->assertEquals($documentation, Documentation::hydrate($documentation->toArray(), $this->model));
    }

    public function test_hydrating_with_section() : void
    {
        $softwareSystem = $this->model->addSoftwareSystem('Software System', 'Description');
        $documentation = new Documentation($this->model);
        $documentation->addSection($softwareSystem, 'test', Format::markdown(), 'teest');
        $this->assertEquals($documentation, Documentation::hydrate($documentation->toArray(), $this->model));
    }

    public function test_hydrating_with_template() : void
    {
        $softwareSystem = $this->model->addSoftwareSystem('Software System', 'Description');
        $documentation = new Documentation($this->model);
        $template = new StructurizrDocumentationTemplate($this->workspace);
        $template->addContextSection($softwareSystem, Format::markdown(), 'Here is some context about the software system...\n\n![](embed:SystemContext)');

        $this->assertEquals($documentation, Documentation::hydrate($documentation->toArray(), $this->model));
    }
}
