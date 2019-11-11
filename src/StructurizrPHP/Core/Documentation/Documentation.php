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

namespace StructurizrPHP\StructurizrPHP\Core\Documentation;

use StructurizrPHP\StructurizrPHP\Assertion;
use StructurizrPHP\StructurizrPHP\Core\Model\Element;
use StructurizrPHP\StructurizrPHP\Core\Model\Model;
use StructurizrPHP\StructurizrPHP\Exception\InvalidArgumentException;

final class Documentation
{
    /**
     * @var Section[]
     */
    private $sections = [];

    /**
     * @var Model
     */
    private $model;

    /**
     * @var TemplateMetadata
     */
    private $template;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function addSection(Element $element, string $title, Format $format, string $content) : Section
    {
        Assertion::notEmpty($title);
        Assertion::notEmpty($content);

        if (!$this->model->contains($element)) {
            throw new InvalidArgumentException(
                sprintf("The element named %s does not exist in the model associated with this documentation.", $element->getName())
            );
        }

        $this->checkSectionIsUnique($element, $title);

        $section = new Section(
            $element,
            $title,
            $this->calculateOrder(),
            $format,
            $content
        );
        $this->sections[] = $section;

        return $section;
    }

    private function checkSectionIsUnique(Element $element = null, string $title = null) : void
    {
        if ($element === null) {
            foreach ($this->sections as $section) {
                if ($title === $section->getTitle()) {
                    throw new InvalidArgumentException(
                        sprintf("A section with a title of %s already exists for this workspace.", $title)
                    );
                }
            }
        } else {
            foreach ($this->sections as $section) {
                if ($title === $section->getTitle()) {
                    throw new InvalidArgumentException(
                        sprintf("A section with a title of %s already exists for the element named %s.", $title, $element->getName())
                    );
                }
            }
        }
    }

    private function calculateOrder() : int
    {
        return count($this->sections) + 1;
    }

    public function setTemplate(TemplateMetadata $template) : void
    {
        $this->template = $template;
    }

    public function toArray() : array
    {
        $data = [
            'sections' => \array_map(function (Section $section) {
                return $section->toArray();
            }, $this->sections),
        ];

        if (isset($this->template)) {
            $data['template'] = $this->template->toArray();
        }

        return $data;
    }

    public static function hydrate(?array $documentationData, Model $model) : Documentation
    {
        $documentation = new self($model);

        $documentationDataModel = new DocumentationDataObject($documentationData);
        $documentation->sections = $documentationDataModel->hydrateSection($model);
        if ($documentationDataModel->templateExist()) {
            $documentation->template = $documentationDataModel->hydrateTemplate();
        }

        return $documentation;
    }
}

final class DocumentationDataObject
{
    /**
     * @var array
     */
    private $documentationSetData;

    public function __construct(array $documentationSetData)
    {
        $this->documentationSetData = $documentationSetData;
    }

    /**
     * @param Model $model
     * @return Section[]
     */
    public function hydrateSection(Model $model) : array
    {
        return \array_map(
            function (array $sectionData) use ($model) {
                return Section::hydrate(
                    $sectionData,
                    $model->getElement($sectionData['elementId']),
                    Format::hydrate($sectionData['format'])
                );
            },
            $this->documentationSetData['sections']??[]
        );
    }

    public function hydrateTemplate() : TemplateMetadata
    {
        return TemplateMetadata::hydrate($this->documentationSetData['template']);
    }

    public function templateExist() : bool
    {
        return isset($this->documentationSetData['template']);
    }
}
