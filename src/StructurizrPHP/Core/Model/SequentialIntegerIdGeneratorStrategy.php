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

namespace StructurizrPHP\StructurizrPHP\Core\Model;

final class SequentialIntegerIdGeneratorStrategy implements IdGenerator
{
    /**
     * @var int
     */
    private $id = 0;

    public function generateId(): string
    {
        $this->id += 1;

        return (string) $this->id;
    }
}
