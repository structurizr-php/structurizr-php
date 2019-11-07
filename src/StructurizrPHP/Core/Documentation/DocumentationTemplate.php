<?php


namespace StructurizrPHP\StructurizrPHP\Core\Documentation;


use StructurizrPHP\StructurizrPHP\Core\Model\Element;
use StructurizrPHP\StructurizrPHP\Core\Model\SoftwareSystem;
use StructurizrPHP\StructurizrPHP\Core\Workspace;

abstract class DocumentationTemplate
{
    /**
     * @var Workspace
     */
    private $workspace;
    private $documentation;

    public function __construct(Workspace $workspace)
    {
        $this->documentation = $workspace->getDocumentation();
        $this->documentation->setTemplate($this->getMetadata());
    }

    public function addSection(SoftwareSystem $softwareSystem, string $title, Format $format, string $content): Section
    {
        return $this->add($softwareSystem, $title, $format, $content);
    }

    private function add(Element $element, string $title, Format $format, string $content): Section
    {
        return $this->documentation->addSection($element, $title, $format, $content);
    }


    abstract protected function getMetadata(): TemplateMetadata;
}