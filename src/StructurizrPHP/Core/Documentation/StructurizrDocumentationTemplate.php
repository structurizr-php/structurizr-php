<?php


namespace StructurizrPHP\StructurizrPHP\Core\Documentation;


use StructurizrPHP\StructurizrPHP\Core\Model\SoftwareSystem;

class StructurizrDocumentationTemplate extends DocumentationTemplate
{

    public function addContextSection(SoftwareSystem $softwareSystem, Format $format, string $content)
    {
        $this->addSection($softwareSystem, "Context", $format, $content);
    }

    protected function getMetadata(): TemplateMetadata
    {
        return new TemplateMetadata("Software Guidebook", "Simon Brown", "https://leanpub.com/visualising-software-architecture");
    }
}