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

namespace StructurizrPHP\StructurizrPHP\Core\Model;

final class Component extends StaticStructureElement
{
    /**
     * @var string
     */
    private $technology;

    /**
     * @var Element
     */
    private $parent;

    /**
     * @var string
     */
    private $type;

    /**
     * @return string
     */
    public function getTechnology() : string
    {
        return $this->technology;
    }

    /**
     * @param string $technology
     */
    public function setTechnology(string $technology) : void
    {
        $this->technology = $technology;
    }

    public function setType(string $type) : void
    {
        $this->type = $type;
    }

    /**
     * @return Element
     */
    public function getParent() : Element
    {
        return $this->parent;
    }

    public function setParent(Element $parent) : void
    {
        $this->parent = $parent;
    }
}
