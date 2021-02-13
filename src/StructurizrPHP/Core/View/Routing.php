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

final class Routing
{
    private $type;

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function direct() : self
    {
        return new self('Direct');
    }

    public static function orthogonal() : self
    {
        return new self('Orthogonal');
    }

    public static function hydrate(string $type) : self
    {
        return new self($type);
    }

    public function type() : string
    {
        return $this->type;
    }
}
