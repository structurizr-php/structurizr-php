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

namespace StructurizrPHP\StructurizrPHP\Core\View;

use StructurizrPHP\StructurizrPHP\Core\Model\Element;

final class ElementView
{
    /**
     * @var Element
     */
    private $element;

    public function __construct(Element $element)
    {
        $this->element = $element;
    }

    public function element(): Element
    {
        return $this->element;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->element->id(),
            'x' => null,
            'y' => null,
        ];
    }
}
