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

namespace StructurizrPHP\Core\Documentation;

use StructurizrPHP\Core\Model\Element;
use StructurizrPHP\Core\Model\SoftwareSystem;
use StructurizrPHP\Core\Workspace;

abstract class DocumentationTemplate
{
    /**
     * @var Documentation
     */
    private $documentation;

    public function __construct(Workspace $workspace)
    {
        $this->documentation = $workspace->getDocumentation();
        $this->documentation->setTemplate($this->getMetadata());
    }

    public function addSection(SoftwareSystem $softwareSystem, string $title, Format $format, string $content) : Section
    {
        return $this->add($softwareSystem, $title, $format, $content);
    }

    private function add(Element $element, string $title, Format $format, string $content) : Section
    {
        return $this->documentation->addSection($element, $title, $format, $content);
    }

    abstract protected function getMetadata() : TemplateMetadata;
}
