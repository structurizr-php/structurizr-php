<?php

/*
 * This file is part of the Structurizr for PHP.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StructurizrPHP\Tests\StructurizrPHP\Tests\Unit\Core\Documentation;

use PHPUnit\Framework\TestCase;
use StructurizrPHP\StructurizrPHP\Core\Documentation\Documentation;
use StructurizrPHP\StructurizrPHP\Core\Documentation\Format;
use StructurizrPHP\StructurizrPHP\Core\Documentation\StructurizrDocumentationTemplate;
use StructurizrPHP\StructurizrPHP\Core\Model\Model;
use StructurizrPHP\StructurizrPHP\Core\Model\Person;
use StructurizrPHP\StructurizrPHP\Core\Workspace;
use StructurizrPHP\StructurizrPHP\Exception\AssertionException;
use StructurizrPHP\StructurizrPHP\Exception\InvalidArgumentException;
use StructurizrPHP\Tests\StructurizrPHP\Tests\Unit\Core\AbstractWorkspaceTestBase;
use TypeError;

final class DocumentationTest extends AbstractWorkspaceTestBase
{
    public function test_null_element_added()
    {
        $this->expectException(TypeError::class);
        $testcase = new Documentation();
        $testcase->addSection(null, 'test', Format::markdown(), 'test content');
    }

    public function test_null_format_added()
    {
        $softwareSystem = $this->model->addSoftwareSystem('Software System', 'Description');
        $testcase = new Documentation($this->model);

        $this->expectException(TypeError::class);

        $testcase->addSection($softwareSystem, 'test', null, 'test content');
    }

    public function test_empty_title_added()
    {
        $softwareSystem = $this->model->addSoftwareSystem('Software System', 'Description');
        $testcase = new Documentation($this->model);

        $this->expectException(AssertionException::class);

        $testcase->addSection($softwareSystem, '', Format::markdown(), 'test content');
    }

    public function test_empty_content_added()
    {
        $softwareSystem = $this->model->addSoftwareSystem('Software System', 'Description');
        $testcase = new Documentation($this->model);

        $this->expectException(AssertionException::class);

        $testcase->addSection($softwareSystem, 'test', Format::markdown(), '');
    }

    /**/
    public function test_addSection_ThrowsAnException_WhenTheRelatedElementIsNotPresentInTheAssociatedModel(): void
    {
        try {
            $softwareSystem = $this->model->addSoftwareSystem('Software System', 'Description');
            $workspace = new Workspace(2, 'test', 'test');
            $workspace->getDocumentation()->addSection($softwareSystem, 'Title', Format::markdown(), 'Content');
            $this->fail();
        } catch (InvalidArgumentException $iae) {
            $this->assertEquals(
                'The element named Software System does not exist in the model associated with this documentation.',
                $iae->getMessage()
            );
        }
    }
    public function test_hydrating_empty_documentation()
    {
        $documentation = new Documentation($this->model);

        $this->assertEquals($documentation, Documentation::hydrate($documentation->toArray(), $this->model));
    }

    public function test_hydrating_with_section()
    {
        $softwareSystem = $this->model->addSoftwareSystem('Software System', 'Description');
        $documentation = new Documentation($this->model);
        $documentation->addSection($softwareSystem, 'test', Format::markdown(), 'teest');
        $this->assertEquals($documentation, Documentation::hydrate($documentation->toArray(), $this->model));
    }

    public function test_hydrating_with_template()
    {
        $softwareSystem = $this->model->addSoftwareSystem('Software System', 'Description');
        $documentation = new Documentation($this->model);
        $template = new StructurizrDocumentationTemplate($this->workspace);
        $template->addContextSection($softwareSystem, Format::markdown(), 'Here is some context about the software system...\n\n![](embed:SystemContext)');

        $this->assertEquals($documentation, Documentation::hydrate($documentation->toArray(), $this->model));
    }
}
