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

final class Properties
{
    private $properties;

    public function __construct(Property ...$properties)
    {
        $this->properties = $properties;
    }

    public function addProperty(Property $property) : void
    {
        $this->properties[] = $property;
    }

    public function toArray() : ?array
    {
        return
            \count($this->properties)
                ?
                \array_merge(
                    ...\array_map(
                        function (Property $property) {
                            return $property->toArray();
                        },
                        $this->properties
                    )
                )
                : null;
    }
}
