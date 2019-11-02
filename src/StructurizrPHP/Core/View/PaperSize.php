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

final class PaperSize
{
    private $size;

    private function __construct(string $size)
    {
        $this->size = $size;
    }

    public function size(): string
    {
        return $this->size;
    }

    public static function a4Portrait() : self
    {
        return new self('A4_Portrait');
    }
}
