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

namespace StructurizrPHP\Core\Model;

use StructurizrPHP\Core\Model\Relationship\InteractionStyle;

abstract class StaticStructureElement extends Element
{
    public function usesSoftwareSystem(
        SoftwareSystem $softwareSystem,
        string $description = 'Uses',
        string $technology = null,
        InteractionStyle $interactionStyle = null
    ) : Relationship {
        return $this->getModel()->addRelationship(
            $this,
            $softwareSystem,
            $description,
            $technology,
            $interactionStyle
        );
    }

    public function usesContainer(
        Container $container,
        string $description = 'Uses',
        string $technology = null,
        InteractionStyle $interactionStyle = null
    ) : Relationship {
        return $this->getModel()->addRelationship(
            $this,
            $container,
            $description,
            $technology,
            $interactionStyle
        );
    }

    public function usesComponent(
        Component $container,
        string $description = 'Uses',
        string $technology = null,
        InteractionStyle $interactionStyle = null
    ) : Relationship {
        return $this->getModel()->addRelationship(
            $this,
            $container,
            $description,
            $technology,
            $interactionStyle
        );
    }

    public function delivers(
        Person $person,
        string $description,
        string $technology = null,
        InteractionStyle $interactionStyle = null
    ) : Relationship {
        return $this->getModel()->addRelationship(
            $this,
            $person,
            $description,
            $technology,
            $interactionStyle
        );
    }
}
