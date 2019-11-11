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

use StructurizrPHP\StructurizrPHP\Core\Assertion;

final class Enterprise
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        Assertion::notEmpty($name);

        $this->name = $name;
    }

    public function name() : string
    {
        return $this->name;
    }
}
