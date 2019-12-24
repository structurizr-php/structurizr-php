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

namespace StructurizrPHP\Core\View;

use StructurizrPHP\Core\Exception\InvalidArgumentException;
use StructurizrPHP\Core\Exception\RuntimeException;
use StructurizrPHP\Core\Model\Element;
use StructurizrPHP\Core\Model\Model;
use StructurizrPHP\Core\Model\Relationship;
use StructurizrPHP\Core\Model\SoftwareSystem;

abstract class View
{
    /**
     * @var SoftwareSystem|null
     */
    protected $softwareSystem;

    /**
     * @var string|null
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $key;

    /**
     * @var PaperSize|null
     */
    private $paperSize;

    /**
     * @var ViewSet
     */
    private $viewSet;

    /**
     * @var ElementView[]
     */
    protected $elementViews;

    /**
     * @var RelationshipView[]
     */
    protected $relationshipsViews;

    /**
     * @var AutomaticLayout|null
     */
    protected $automaticLayout;

    /**
     * @var LayoutMergeStrategy
     */
    private $layoutMergeStrategy;

    public function __construct(
        ?SoftwareSystem $softwareSystem,
        string $description,
        string $key,
        ViewSet $viewSet
    ) {
        $this->softwareSystem = $softwareSystem;
        $this->description = $description;
        $this->key = $key;
        $this->viewSet = $viewSet;
        $this->elementViews = [];
        $this->relationshipsViews = [];
        $this->layoutMergeStrategy = new DefaultLayoutMergeStrategy();
    }

    protected function getModel() : ?Model
    {
        return $this->softwareSystem ? $this->softwareSystem->getModel() : null;
    }

    public function keyEquals(View $other) : bool
    {
        return $this->key === $other->key;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title) : void
    {
        $this->title = $title;
    }

    /**
     * @return ElementView[]
     */
    public function getElements() : array
    {
        return $this->elementViews;
    }

    /**
     * @return RelationshipView[]
     */
    public function getRelationships() : array
    {
        return $this->relationshipsViews;
    }

    public function getRelationshipView(Relationship $relationship) : RelationshipView
    {
        foreach ($this->relationshipsViews as $relationshipView) {
            $nextRelationship = $relationshipView->getRelationship();
            if ($nextRelationship && $nextRelationship->id() === $relationship->id()) {
                return $relationshipView;
            }
        }

        throw new RuntimeException(\sprintf("Relationship view for relationship with id \"%s\" does not exists", $relationship->id()));
    }

    protected function addRelationshipWithDescription(Relationship $relationship, string $description, string $order) : RelationshipView
    {
        $relationshipView = $this->addRelationship($relationship);
        if (!$relationshipView) {
            throw new InvalidArgumentException("Can\'t add Relationship");
        }

        $relationshipView->setDescription($description);
        $relationshipView->setOrder($order);

        return $relationshipView;
    }

    protected function addRelationship(Relationship $relationship) : ?RelationshipView
    {
        if ($this->isElementInView($relationship->getSource()) && $this->isElementInView($relationship->getDestination())) {
            $relationshipView = new RelationshipView($relationship);
            $this->relationshipsViews[] = $relationshipView;

            return $relationshipView;
        }

        return null;
    }

    protected function isElementInView(Element $element) : bool
    {
        return (bool) \array_filter(
            $this->elementViews,
            function (ElementView $ev) use ($element) {
                return $ev->element()->equals($element);
            }
        );
    }

    public function setPaperSize(?PaperSize $paperSize) : void
    {
        $this->paperSize = $paperSize;
    }

    public function getPaperSize() : ?PaperSize
    {
        return $this->paperSize;
    }

    public function setAutomaticLayout(bool $enabled) : void
    {
        if ($enabled) {
            $this->automaticLayout = new AutomaticLayout(RankDirection::topBottom(), 300, 600, 200, false);
        } else {
            $this->automaticLayout = null;
        }
    }

    public function addElement(Element $element, bool $addRelationships = true) : ElementView
    {
        $elementView = new ElementView($element);

        if ($addRelationships) {
            $this->addRelationships($element);
        }

        $this->elementViews[] = $elementView;

        return $elementView;
    }

    private function addRelationships(Element $element) : void
    {
        foreach ($element->getRelationships() as $relationship) {
            foreach ($this->elementViews as $e) {
                if ($e->element()->equals($relationship->getDestination())) {
                    $this->relationshipsViews[] = new RelationshipView($relationship);
                }
            }
        }

        foreach ($this->elementViews as $e) {
            foreach ($e->element()->getRelationships() as $r) {
                if ($r->getDestination()->equals($element)) {
                    $this->relationshipsViews[] = new RelationshipView($r);
                }
            }
        }
    }

    public function copyLayoutInformationFrom(?View $source) : void
    {
        if ($source) {
            $this->layoutMergeStrategy->copyLayoutInformation($source, $this);
        }
    }

    public function toArray() : array
    {
        $data = [
            'title' => $this->title ? $this->title : null,
            'description' => $this->description,
            'key' => $this->key,
            'paperSize' => $this->paperSize ? $this->paperSize->size() : null,
            'automaticLayout' => $this->automaticLayout ? $this->automaticLayout->toArray() : null,
            'elements' => \array_map(
                function (ElementView $elementView) {
                    return $elementView->toArray();
                },
                $this->elementViews
            ),
            'relationships' => \array_map(
                function (RelationshipView $relationshipView) {
                    return $relationshipView->toArray();
                },
                $this->relationshipsViews
            ),
        ];

        return $data;
    }

    public static function hydrateView(View $view, array $viewData) : void
    {
        if (isset($viewData['key'])) {
            $view->key = $viewData['key'];
        }

        if (isset($viewData['title'])) {
            $view->title = $viewData['title'];
        }

        if (isset($viewData['paperSize'])) {
            $view->paperSize = PaperSize::hydrate($viewData['paperSize']);
        }

        if (isset($viewData['description'])) {
            $view->description = $viewData['description'];
        }

        if (isset($viewData['elements'])) {
            foreach ($viewData['elements'] as $elementData) {
                $view->elementViews[] = ElementView::hydrate($elementData, $view->viewSet->getModel()->getElement($elementData['id']));
            }
        }

        if (isset($viewData['automaticLayout'])) {
            $view->automaticLayout = AutomaticLayout::hydrate($viewData['automaticLayout']);
        }

        if (isset($viewData['relationships'])) {
            foreach ($viewData['relationships'] as $relationshipData) {
                $relationshipView = RelationshipView::hydrate(
                    $view->viewSet->getModel()->getRelationship($relationshipData['id']),
                    $relationshipData
                );

                $view->relationshipsViews[] = $relationshipView;
            }
        }
    }
}
