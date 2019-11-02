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

abstract class StaticView extends View
{
    public function addAllSoftwareSystems(bool $addRelationships = true) : void
    {
        foreach ($this->model()->softwareSystems() as $softwareSystem) {
            $this->addElement($softwareSystem, $addRelationships);
        }
    }

    public function addAllPeople(bool $addRelationships = true) : void
    {
        foreach ($this->model()->people() as $person) {
            $this->addElement($person, $addRelationships);
        }
    }

    abstract public function addAllElements() : void;
}
