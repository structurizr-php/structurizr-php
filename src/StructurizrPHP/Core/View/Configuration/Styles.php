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

namespace StructurizrPHP\Core\View\Configuration;

use StructurizrPHP\Core\Assertion;

final class Styles
{
    /**
     * @var ElementStyle[]
     */
    private $elementsStyles;

    /**
     * @var RelationshipStyle[]
     */
    private $relationshipsStyles;

    public function __construct()
    {
        $this->elementsStyles = [];
        $this->relationshipsStyles = [];
    }

    public static function hydrate(array $stylesData) : self
    {
        $styles = new self();

        if (isset($stylesData['elements'])) {
            foreach ($stylesData['elements'] as $elementData) {
                $styles->elementsStyles[$elementData['tag']] = ElementStyle::hydrate($elementData);
            }
        }

        if (isset($stylesData['relationships'])) {
            foreach ($stylesData['relationships'] as $relationshipData) {
                $styles->relationshipsStyles[$relationshipData['tag']] = RelationshipStyle::hydrate($relationshipData);
            }
        }

        return $styles;
    }

    public function addElementStyle(string $tag) : ElementStyle
    {
        Assertion::keyNotExists($this->elementsStyles, $tag, \sprintf('An element style for the tag "%s" already exists .', $tag));

        $elementStyle = new ElementStyle($tag);

        $this->elementsStyles[$tag] = $elementStyle;

        return $elementStyle;
    }

    public function addRelationshipStyle(string $tag) : RelationshipStyle
    {
        Assertion::keyNotExists($this->relationshipsStyles, $tag, \sprintf('A relationship style for the tag "%s" already exists .', $tag));

        $relationshipStyle = new RelationshipStyle($tag);

        $this->relationshipsStyles[$tag] = $relationshipStyle;

        return $relationshipStyle;
    }

    public function toArray() : array
    {
        return [
            'elements' => \array_values(\array_map(function (ElementStyle $elementStyle) {
                return $elementStyle->toArray();
            }, $this->elementsStyles)),
            'relationships' => \array_values(\array_map(function (RelationshipStyle $relationshipStyle) {
                return $relationshipStyle->toArray();
            }, $this->relationshipsStyles)),
        ];
    }
}
