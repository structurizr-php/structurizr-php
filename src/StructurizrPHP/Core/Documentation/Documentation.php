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

use StructurizrPHP\Core\Assertion;
use StructurizrPHP\Core\Exception\InvalidArgumentException;
use StructurizrPHP\Core\Model\Element;
use StructurizrPHP\Core\Model\Model;
use StructurizrPHP\Core\Model\SoftwareSystem;

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

    /**
     * @var array
     */
    private $decisions;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->decisions = [];
    }

    public static function hydrate(?array $documentationData, Model $model) : self
    {
        $documentation = new self($model);

        $documentationDataModel = new DocumentationDataObject($documentationData);
        $documentation->sections = $documentationDataModel->hydrateSections($model);
        $documentation->decisions = $documentationDataModel->hydrateDecisions($model);

        if ($documentationDataModel->templateExist()) {
            $documentation->template = $documentationDataModel->hydrateTemplate();
        }

        return $documentation;
    }

    public function addSection(Element $element, string $title, Format $format, string $content) : Section
    {
        Assertion::notEmpty($title);
        Assertion::notEmpty($content);

        if (!$this->model->contains($element)) {
            throw new InvalidArgumentException(
                \sprintf('The element named %s does not exist in the model associated with this documentation.', $element->getName())
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

    public function setTemplate(TemplateMetadata $template) : void
    {
        $this->template = $template;
    }

    public function isEmpty() : bool
    {
        return $this->template === null && !\count($this->sections) && !\count($this->decisions);
    }

    public function toArray() : array
    {
        $data = [];

        if (\count($this->sections)) {
            $data['sections'] = \array_map(function (Section $section) {
                return $section->toArray();
            }, $this->sections);
        }

        if (\count($this->decisions)) {
            $data['decisions'] = \array_map(function (Decision $decisions) {
                return $decisions->toArray();
            }, $this->decisions);
        }

        if (isset($this->template)) {
            $data['template'] = $this->template->toArray();
        }

        return $data;
    }

    public function addDecision(SoftwareSystem $softwareSystem, string $id, \DateTimeImmutable $date, string $title, DecisionStatus $status, Format $format, string $content) : Decision
    {
        $decision = new Decision($softwareSystem, $id, $date, $title, $status, $format, $content);
        $this->decisions[] = $decision;

        return $decision;
    }

    private function checkSectionIsUnique(Element $element = null, string $title = null) : void
    {
        if ($element === null) {
            foreach ($this->sections as $section) {
                if ($title === $section->getTitle()) {
                    throw new InvalidArgumentException(
                        \sprintf('A section with a title of %s already exists for this workspace.', $title)
                    );
                }
            }
        } else {
            foreach ($this->sections as $section) {
                if ($title === $section->getTitle()) {
                    throw new InvalidArgumentException(
                        \sprintf('A section with a title of %s already exists for the element named %s.', $title, $element->getName())
                    );
                }
            }
        }
    }

    private function calculateOrder() : int
    {
        return \count($this->sections) + 1;
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
     *
     * @return Section[]
     */
    public function hydrateSections(Model $model) : array
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

    /**
     * @param Model $model
     *
     * @return Decision[]
     */
    public function hydrateDecisions(Model $model) : array
    {
        return \array_map(
            function (array $decisionData) use ($model) {
                return Decision::hydrate(
                    $decisionData,
                    $model->getElement($decisionData['elementId']),
                );
            },
            $this->documentationSetData['decisions'] ?? []
        );
    }
}
