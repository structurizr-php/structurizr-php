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

final class Relationship extends ModelItem
{
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
    private $description;

    /**
     * @var null|string
     */
    private $technology;

    /**
     * @var InteractionStyle
     */
    private $interactionStyle;

    /**
     * @var null|string
     */
    private $linkedRelationshipId;

    public function __construct(
        string $id,
        Element $source,
        Element $destination,
        string $description
    ) {
        parent::__construct($id);

        $this->description = $description;
        $this->source = $source;
        $this->destination = $destination;
        $this->interactionStyle = InteractionStyle::synchronous();

        if ($this->interactionStyle === InteractionStyle::synchronous()) {
            $this->addTags(Tags::SYNCHRONOUS);
        } else {
            $this->addTags(Tags::ASYNCHRONOUS);
        }

        $this->addTags(Tags::RELATIONSHIP);
    }

    public static function hydrate(array $relationshipData, Element $source, Model $model) : self
    {
        $relationship = new self(
            $relationshipData['id'],
            $source,
            $model->getElement($relationshipData['destinationId']),
            isset($relationshipData['description']) ? (string) $relationshipData['description'] : '',
        );

        if (isset($relationshipData['interactionStyle'])) {
            $relationship->setInteractionStyle(InteractionStyle::hydrate($relationshipData['interactionStyle']));
        }

        if (isset($relationshipData['technology'])) {
            $relationship->setTechnology($relationshipData['technology']);
        }

        if (isset($relationshipData['linkedRelationshipId'])) {
            $relationship->linkedRelationshipId = $relationshipData['linkedRelationshipId'];
        }

        parent::hydrateModelItem($relationship, $relationshipData, $model);

        $model->addRelationshipToInternalStructures($relationship);

        return $relationship;
    }

    /**
     * @param InteractionStyle $interactionStyle
     */
    public function setInteractionStyle(InteractionStyle $interactionStyle) : void
    {
        $this->interactionStyle = $interactionStyle;
    }

    public function getInteractionStyle() : InteractionStyle
    {
        return $this->interactionStyle;
    }

    public function getTechnology() : ?string
    {
        return $this->technology;
    }

    public function setTechnology(?string $technology) : void
    {
        $this->technology = $technology;
    }

    public function setDescription(string $description) : void
    {
        $this->description = $description;
    }

    public function getDestination() : Element
    {
        return $this->destination;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function setLinkedRelationshipId(?string $linkedRelationshipId) : void
    {
        $this->linkedRelationshipId = $linkedRelationshipId;
    }

    public function getSource() : Element
    {
        return $this->source;
    }

    public function toArray() : array
    {
        $data = \array_merge(
            [
                'description' => $this->description,
                'sourceId' => $this->source->id(),
                'destinationId' => $this->destination->id(),
                'interactionStyle' => $this->interactionStyle->style(),
            ],
            parent::toArray()
        );

        if (isset($this->linkedRelationshipId)) {
            $data['linkedRelationshipId'] = $this->linkedRelationshipId;
        }

        if (isset($this->technology)) {
            $data['technology'] = $this->technology;
        }

        return $data;
    }
}
