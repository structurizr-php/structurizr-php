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

final class SoftwareSystem extends StaticStructureElement
{
    /**
     * @var Location
     */
    private $location;

    public function __construct(string $id, string $name, string $description, Location $location, Model $model)
    {
        parent::__construct($id, $name, $description, $model);
        $this->location = $location;
        $this->setTags(new Tags(Tags::ELEMENT, Tags::SOFTWARE_SYSTEM));
    }

    public function toArray() : array
    {
        return \array_merge(
            [
                'location' => $this->location->type(),
            ],
            parent::toArray()
        );
    }
}
