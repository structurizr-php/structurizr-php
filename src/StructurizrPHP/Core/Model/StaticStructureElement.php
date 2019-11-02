<?php

declare(strict_types=1);

/*
 * This file is part of the Structurizr SDK for PHP.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StructurizrPHP\StructurizrPHP\Core\Model;

use StructurizrPHP\StructurizrPHP\Core\Model\Relationship\InteractionStyle;

abstract class StaticStructureElement extends Element
{
    public function usesSoftwareSystem(
        SoftwareSystem $softwareSystem,
        string $description,
        string $technology,
        InteractionStyle $interactionStyle = null
    ) : void {
        $this->model()->addRelationship($this, $softwareSystem, $description, $technology, $interactionStyle ? $interactionStyle : InteractionStyle::synchronous());
    }
}
