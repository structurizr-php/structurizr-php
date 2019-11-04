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

use StructurizrPHP\StructurizrPHP\Assertion;
use StructurizrPHP\StructurizrPHP\Core\Model\Element;

final class ElementView
{
    /**
     * @var Element
     */
    private $element;

    /**
     * @var int|null
     */
    private $x;

    /**
     * @var int|null
     */
    private $y;

    public function __construct(Element $element)
    {
        $this->element = $element;
    }

    public function element(): Element
    {
        return $this->element;
    }

    public function setX(int $x) : self
    {
        Assertion::greaterThan($x, 0);

        $this->x = $x;

        return $this;
    }

    public function sety(int $y) : self
    {
        Assertion::greaterThan($y, 0);

        $this->y = $y;

        return $this;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->element->id(),
            'x' => $this->x,
            'y' => null,
        ];
    }
}
