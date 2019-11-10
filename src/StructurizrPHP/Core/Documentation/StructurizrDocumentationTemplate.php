<?php

/*
 * This file is part of the Structurizr for PHP.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StructurizrPHP\StructurizrPHP\Core\Documentation;

use StructurizrPHP\StructurizrPHP\Core\Model\SoftwareSystem;

class StructurizrDocumentationTemplate extends DocumentationTemplate
{
    public function addContextSection(SoftwareSystem $softwareSystem, Format $format, string $content) : void
    {
        $this->addSection($softwareSystem, "Context", $format, $content);
    }

    protected function getMetadata() : TemplateMetadata
    {
        return new TemplateMetadata("Software Guidebook", "Simon Brown", "https://leanpub.com/visualising-software-architecture");
    }
}
