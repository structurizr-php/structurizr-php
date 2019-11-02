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

namespace StructurizrPHP\StructurizrPHP\Core\View\Configuration;

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

    public function addElementStyle(string $tag) : ElementStyle
    {
        $elementStyle = new ElementStyle($tag);

        $this->elementsStyles[] = $elementStyle;

        return $elementStyle;
    }

    public function toArray() : array
    {
        return [
            'elements' => \array_map(function (ElementStyle $elementStyle) {
                return $elementStyle->toArray();
            }, $this->elementsStyles),
        ];
    }
}
