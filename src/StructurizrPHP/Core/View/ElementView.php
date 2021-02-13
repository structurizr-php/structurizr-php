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

final class ElementView
{
    /**
     * @var Element
     */
    private $element;

    /**
     * @var null|int
     */
    private $x;

    /**
     * @var null|int
     */
    private $y;

    public function __construct(Element $element)
    {
        $this->element = $element;
    }

    public static function hydrate(array $elementViewData, Element $element) : self
    {
        $elementView = new self($element);

        if (\array_key_exists('x', $elementViewData) && \array_key_exists('y', $elementViewData)) {
            $elementView
                ->setX((int) $elementViewData['x'])
                ->setY((int) $elementViewData['y']);
        }

        return $elementView;
    }

    public function element() : Element
    {
        return $this->element;
    }

    public function setX(?int $x) : self
    {
        $this->x = $x;

        return $this;
    }

    public function setY(?int $y) : self
    {
        $this->y = $y;

        return $this;
    }

    public function getX() : ?int
    {
        return $this->x;
    }

    public function getY() : ?int
    {
        return $this->y;
    }

    public function copyLayoutInformationFrom(self $source) : void
    {
        $this->setX($source->getX());
        $this->setY($source->getY());
    }

    public function toArray() : array
    {
        return [
            'id' => $this->element->id(),
            'x' => $this->x,
            'y' => $this->y,
        ];
    }
}
