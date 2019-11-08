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

namespace StructurizrPHP\StructurizrPHP\Core\View;

use StructurizrPHP\StructurizrPHP\Core\Model\Container;
use StructurizrPHP\StructurizrPHP\Core\Model\Element;
use StructurizrPHP\StructurizrPHP\Core\Model\Model;
use StructurizrPHP\StructurizrPHP\Core\Model\Person;
use StructurizrPHP\StructurizrPHP\Core\Model\SoftwareSystem;
use StructurizrPHP\StructurizrPHP\Exception\InvalidArgumentException;

final class DynamicView extends View
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var SoftwareSystem|Container|null
     */
    private $element;

    /**
     * @var SequenceNumber
     */
    private $sequenceNumber;

    /**
     * @psalm-suppress ArgumentTypeCoercion
     * @psalm-suppress PropertyTypeCoercion
     */
    public function __construct(Model $model, string $key, string $description, ViewSet $viewSet, ?Element $element = null)
    {
        parent::__construct($element, $key, $description, $viewSet);
        $this->model = $model;
        $this->sequenceNumber = new SequenceNumber();
        $this->element = $element;
    }

    protected function getModel(): ?Model
    {
        return $this->model;
    }

    public static function softwareSystem(SoftwareSystem $softwareSystem, string $key, string $description, ViewSet $viewSet) : self
    {
        $view = new self($softwareSystem->model(), $description, $key, $viewSet, $softwareSystem);
        $view->model = $softwareSystem->model();
        $view->element = $softwareSystem;

        return $view;
    }

    public static function container(Container $container, string $key, string $description, ViewSet $viewSet) : self
    {
        $view = new self($container->model(), $key, $description, $viewSet, $container);
        $view->softwareSystem = $container->getParent();
        $view->element = $container->getParent();

        return $view;
    }

    public function add(Element $source, string $description, Element $destination) : RelationshipView
    {
        $this->checkElement($source);
        $this->checkElement($destination);

        $relationship = $source->getEfferentRelationshipWith($destination);
        $this->addElement($source, false);
        $this->addElement($destination, false);

        return $this->addRelationshipWithDescription($relationship, $description, $this->sequenceNumber->getNext());
    }

    /**
     * @psalm-suppress PossiblyNullArgument
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress PossiblyNullOperand
     * @psalm-suppress PossiblyUndefinedMethod
     * @psalm-suppress MixedArgument
     */
    private function checkElement(Element $elementToBeAdded) : void
    {
        if (!($elementToBeAdded instanceof Person) && !($elementToBeAdded instanceof SoftwareSystem) && !($elementToBeAdded instanceof Container) /*&& !(elementToBeAdded instanceof Component)*/) {
            throw new InvalidArgumentException("Only people, software systems, containers and components can be added to dynamic views.");
        }

        // people can always be added
        if ($elementToBeAdded instanceof Person) {
            return;
        }

        if ($this->element instanceof SoftwareSystem) {
            if ($elementToBeAdded->equals($this->element)) {
                throw new InvalidArgumentException($elementToBeAdded->getName() . " is already the scope of this view and cannot be added to it.");
            }

            if ($elementToBeAdded instanceof Container && !$elementToBeAdded->getParent()->equals($this->element)) {
                throw new InvalidArgumentException(\sprintf("Only containers that reside inside \"%s\" can be added to this view.", + $this->element->getName()));
            }
            /* TODO: Add with Component
             *
            if (elementToBeAdded instanceof Component) {
                throw new IllegalArgumentException("Components can't be added to a dynamic view when the scope is a software system.");
            }
            */
        }

        // if the scope of this dynamic view is a container, we only want other containers inside the same software system
        // and other components inside the container
        if ($this->element instanceof Container) {
            if ($elementToBeAdded->equals($this->element) || $elementToBeAdded->equals($this->element->getParent())) {
                throw new InvalidArgumentException($elementToBeAdded->getName() . " is already the scope of this view and cannot be added to it.");
            }

            if ($elementToBeAdded instanceof Container && !$elementToBeAdded->getParent()->equals($this->element->getParent())) {
                throw new InvalidArgumentException("Only containers that reside inside " . $this->element->getName() . " can be added to this view.");
            }

            /* TODO: Add with Component
             *
            if (elementToBeAdded instanceof Component && !elementToBeAdded.getParent().equals(element)) {
                throw new IllegalArgumentException("Only components that reside inside " + element.getName() + " can be added to this view.");
            }
            */
        }

        if ($this->element === null) {
            if (!($elementToBeAdded instanceof SoftwareSystem)) {
                throw new InvalidArgumentException("Only people and software systems can be added to this dynamic view.");
            }
        }
    }

    /**
     * @psalm-suppress PossiblyNullReference
     */
    public function toArray() : array
    {
        return \array_merge(
            [
                'softwareSystemId' => $this->element instanceof Container ? $this->element->getParent()->id() : $this->element->id(),
            ],
            parent::toArray()
        );
    }

    /**
     * @psalm-suppress InvalidArgument
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedAssignment
     * @psalm-suppress ArgumentTypeCoercion
     * @psalm-suppress PossiblyNullReference
     */
    public static function hydrate(array $viewData, ViewSet $viewSet) : self
    {
        $view = new self(
            $viewSet->model(),
            $viewData['description'],
            $viewData['key'],
            $viewSet,
            isset($viewData['softwareSystemId']) ? $viewSet->model()->getElement($viewData['softwareSystemId']) : null
        );

        if (isset($viewData['paperSize'])) {
            $view->setPaperSize(PaperSize::hydrate($viewData['paperSize']));
        }

        if (isset($viewData['elements'])) {
            foreach ($viewData['elements'] as $elementData) {
                $elementView = $view->addElement($viewSet->model()->getElement($elementData['id']), false);

                if (isset($elementData['x']) && isset($elementData['y'])) {
                    $elementView
                        ->setX((int)$elementData['x'])
                        ->setY((int)$elementData['y']);
                }
            }
        }

        if (isset($viewData['relationships'])) {
            foreach ($viewData['relationships'] as $relationshipData) {
                $relationshipView = RelationshipView::hydrate(
                    $view->getModel()->getRelationship($relationshipData['id']),
                    $relationshipData
                );

                $view->relationshipsViews[] = $relationshipView;
                $view->sequenceNumber->getNext();
            }
        }

        if (isset($viewData['automaticLayout'])) {
            $view->automaticLayout = AutomaticLayout::hydrate($viewData['automaticLayout']);
        }

        return $view;
    }
}
