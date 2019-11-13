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

use StructurizrPHP\Core\Model\Relationship;

/**
 * A default implementation of a LayoutMergeStrategy that:
 *
 * - Sets the paper size (if not set).
 * - Copies element x,y positions.
 * - Copies relationship vertices. - TODO
 *
 * Elements are matched by the full canonical name. The downside of this approach is that if an element is renamed
 * between versions of a workspace, it won't be possible to find/copy the layout information associated with an element.
 */
final class DefaultLayoutMergeStrategy implements LayoutMergeStrategy
{
    public function copyLayoutInformation(View $sourceView, View $destinationView) : void
    {
        if (!$destinationView->getPaperSize() && $sourceView->getPaperSize()) {
            $destinationView->setPaperSize($sourceView->getPaperSize());
        }

        foreach ($destinationView->getElements() as $destinationElementView) {
            $sourceElementView = \current(\array_filter(
                $sourceView->getElements(),
                function (ElementView $elementView) use ($destinationElementView) {
                    return $elementView->element()->getCanonicalName() === $destinationElementView->element()->getCanonicalName();
                }
            ));

            if ($sourceElementView instanceof ElementView) {
                $destinationElementView->copyLayoutInformationFrom($sourceElementView);
            }
        }

        foreach ($destinationView->getRelationships() as $destinationRelationshipView) {
            $sourceRelationshipView = ($destinationView instanceof DynamicView)
                ? $this->findRelationshipViewByRelationshipView($sourceView, $destinationRelationshipView)
                : $this->findRelationshipViewByRelationship($sourceView, $destinationRelationshipView->getRelationship());

            if ($sourceRelationshipView !== null) {
                $destinationRelationshipView->copyLayoutInformationFrom($sourceRelationshipView);
            }
        }
    }

    protected function findRelationshipViewByRelationship(View $view, Relationship $relationship) : ?RelationshipView
    {
        foreach ($view->getRelationships() as $rv) {
            if (
                ($rv->getRelationship()->getSource()->getCanonicalName() === $relationship->getSource()->getCanonicalName()) &&
                ($rv->getRelationship()->getDestination()->getCanonicalName() === $relationship->getDestination()->getCanonicalName()) &&
                ($rv->getRelationship()->getDescription() === $relationship->getDescription())
            ) {
                return $rv;
            }
        }

        return null;
    }

    protected function findRelationshipViewByRelationshipView(View $view, RelationshipView $relationshipView) : ?RelationshipView
    {
        foreach ($view->getRelationships() as $rv) {
            if (
                $rv->getRelationship()->getSource()->getCanonicalName() === $relationshipView->getRelationship()->getSource()->getCanonicalName() &&
                $rv->getRelationship()->getDestination()->getCanonicalName() === $relationshipView->getRelationship()->getDestination()->getCanonicalName() &&
                $rv->getDescription() === $relationshipView->getDescription() &&
                $rv->getOrder() === $relationshipView->getOrder()
            ) {
                return $rv;
            }
        }

        return null;
    }
}
