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

use StructurizrPHP\Core\Model\Element;
use StructurizrPHP\Core\Model\Person;
use StructurizrPHP\Core\Model\Relationship;
use StructurizrPHP\Core\Model\SoftwareSystem;

abstract class StaticView extends View
{
    public function addSoftwareSystem(SoftwareSystem $softwareSystem, bool $addRelationships = true) : void
    {
        $this->addElement($softwareSystem, $addRelationships);
    }

    public function removeSoftwareSystem(SoftwareSystem $softwareSystem) : void
    {
        $this->removeElement($softwareSystem);
    }

    public function addPerson(Person $person, bool $addRelationships = true) : void
    {
        $this->addElement($person, $addRelationships);
    }

    public function removePerson(Person $person) : void
    {
        $this->removeElement($person);
    }

    public function addAllSoftwareSystems(bool $addRelationships = true) : void
    {
        $model = $this->getModel();

        if (null === $model) {
            return;
        }

        foreach ($model->softwareSystems() as $softwareSystem) {
            $this->addElement($softwareSystem, $addRelationships);
        }
    }

    public function addAllPeople(bool $addRelationships = true) : void
    {
        $model = $this->getModel();

        if (null === $model) {
            return;
        }

        foreach ($model->people() as $person) {
            $this->addElement($person, $addRelationships);
        }
    }

    public function addRelationship(Relationship $relationship) : ?RelationshipView
    {
        return parent::addRelationship($relationship);
    }

    public function removeRelationship(Relationship $relationship) : void
    {
        parent::removeRelationship($relationship);
    }

    abstract public function addAllElements() : void;

    protected function addNearestTypeNeighbours(Element $element, string $typeOfElement) : void
    {
        $this->addElement($element);

        $destinations = \array_map(
            function (Relationship $relationship) {
                return $relationship->getDestination();
            },
            \array_filter(
                $this->getModel()->getRelationships(),
                function (Relationship $relationship) use ($element, $typeOfElement) {
                    return $relationship->getSource()->equals($element) && \get_class($relationship->getDestination()) === $typeOfElement;
                }
            )
        );

        foreach ($destinations as $destination) {
            $this->addElement($destination);
        }

        $sources = \array_map(
            function (Relationship $relationship) {
                return $relationship->getSource();
            },
            \array_filter(
                $this->getModel()->getRelationships(),
                function (Relationship $relationship) use ($element, $typeOfElement) {
                    return $relationship->getDestination()->equals($element) && \get_class($relationship->getSource()) === $typeOfElement;
                }
            )
        );

        foreach ($sources as $source) {
            $this->addElement($source);
        }
    }
}
