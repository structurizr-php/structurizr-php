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

use StructurizrPHP\StructurizrPHP\Assertion;
use StructurizrPHP\StructurizrPHP\Core\Model\Relationship\InteractionStyle;

final class Relationship extends ModelItem
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var Element
     */
    private $source;

    /**
     * @var Element
     */
    private $destination;

    /**
     * @var string
     */
    private $technology;

    /**
     * @var InteractionStyle
     */
    private $interactionStyle;

    public function __construct(
        string $id,
        Element $source,
        Element $destination,
        string $description,
        string $technology,
        InteractionStyle $interactionStyle
    ) {
        parent::__construct($id);
        Assertion::notEmpty($description);
        Assertion::notEmpty($technology);

        $this->description = $description;
        $this->source = $source;
        $this->destination = $destination;
        $this->technology = $technology;
        $this->interactionStyle = $interactionStyle;
    }

    public function destination(): Element
    {
        return $this->destination;
    }

    public function source(): Element
    {
        return $this->source;
    }

    public function toArray(): array
    {
        return \array_merge(
            [
                'description' => $this->description,
                'sourceId' => $this->source->id(),
                'destinationId' => $this->destination->id(),
                'technology' => $this->technology,
                'interactionStyle' => $this->interactionStyle->style(),
            ],
            parent::toArray()
        );
    }
}
