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
use StructurizrPHP\StructurizrPHP\Core\Model\Person;
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

    public static function hydrate(?array $documentationData, Model $model):Documentation
    {
        $documentation = new self($model);

        $documentationDataModel = new DocumentationDataModel($documentationData);
        $documentation->sections = $documentationDataModel->hydrateSection($model);
        if ($documentationDataModel->templateExist()) {
            $documentation->template = $documentationDataModel->hydrateTemplate();
        }

        return $documentation;
    }

    public function addSection(Element $element = null, string $title = null, Format $format = null, string $content = null)
    {
        if ($element !== null && !$this->model->contains($element)) {
            throw new InvalidArgumentException(
                sprintf("The element named %s does not exist in the model associated with this documentation.", $element->getName())
            );
        }
        $this->checkTitleIsSpecified($title);
        $this->checkContentIsSpecified($content);
        $this->checkSectionIsUnique($element, $title);
        $this->checkFormatIsSpecified($format);

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

    private function checkTitleIsSpecified(string $title)
    {
        Assertion::minLength($title, 1);
    }

    private function checkContentIsSpecified(string $content)
    {
        Assertion::minLength($content, 1);
    }

    private function checkFormatIsSpecified(Format $format)
    {
    }

    private function checkSectionIsUnique(Element $element = null, string $title = null)
    {
        if ($element === null) {
            foreach ($this->sections as $section) {
                if ($section->getElement() === null && $title === $section->getTitle()) {
                    throw new InvalidArgumentException(
                        sprintf("A section with a title of %s already exists for this workspace.", $title)
                    );
                }
            }
        } else {
            foreach ($this->sections as $section) {
                if ($element->getId() === $section->getElementId() && $title === $section->getTitle()) {
                    throw new InvalidArgumentException(
                        sprintf("A section with a title of %s already exists for the element named %s.", $title, $element->getName())
                    );
                }
            }
        }
    }

    private function calculateOrder(): int
    {
        return count($this->sections) + 1;
    }

    public function setTemplate(TemplateMetadata $template)
    {
        $this->template = $template;
    }

    public function toArray(): array
    {
        $data = [
            'sections' => [],
        ];
        if (isset($this->template)) {
            $data['template'] = $this->template->toArray();
        }
        if (!\count($this->sections)) {
            return $data;
        }
        if (\count($this->sections)) {
            $data['sections'] = \array_map(function (Section $section) {
                return $section->toArray();
            }, $this->sections);
        }

        return $data;
    }
}
final class DocumentationDataModel
{
    /**
     * @var array
     */
    private $documentationSetData;

    public function __construct(array $documentationSetData)
    {
        $this->documentationSetData = $documentationSetData;
    }

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

    public function hydrateTemplate():TemplateMetadata
    {
        return TemplateMetadata::hydrate($this->documentationSetData['template']);
    }

    public function templateExist()
    {
        return isset($this->documentationSetData['template']);
    }
}
