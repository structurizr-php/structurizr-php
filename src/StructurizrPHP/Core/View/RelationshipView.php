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

use StructurizrPHP\StructurizrPHP\Core\Model\Relationship;

final class RelationshipView
{
    /**
     * @var Relationship
     */
    private $relationship;

    public function __construct(Relationship $relationship)
    {
        $this->relationship = $relationship;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->relationship->id(),
            'description' => null,
            'order' => null,
            'vertices' => [],
            'position' => null,
        ];
    }
}
