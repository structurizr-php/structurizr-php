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

/**
 * These represent paper sizes in pixels at 300dpi.
 */
final class PaperSize
{
    private $size;

    private function __construct(string $size)
    {
        $this->size = $size;
    }

    public static function A6_Portrait() : self
    {
        return new self('A6_Portrait');
    }

    public static function A6_Landscape() : self
    {
        return new self('A6_Landscape');
    }

    public static function A5_Portrait() : self
    {
        return new self('A5_Portrait');
    }

    public static function A5_Landscape() : self
    {
        return new self('A5_Landscape');
    }

    public static function A4_Portrait() : self
    {
        return new self('A4_Portrait');
    }

    public static function A4_Landscape() : self
    {
        return new self('A4_Landscape');
    }

    public static function A3_Portrait() : self
    {
        return new self('A3_Portrait');
    }

    public static function A3_Landscape() : self
    {
        return new self('A3_Landscape');
    }

    public static function A2_Portrait() : self
    {
        return new self('A2_Portrait');
    }

    public static function A2_Landscape() : self
    {
        return new self('A2_Landscape');
    }

    public static function Letter_Portrait() : self
    {
        return new self('Letter_Portrait');
    }

    public static function Letter_Landscape() : self
    {
        return new self('Letter_Landscape');
    }

    public static function Legal_Portrait() : self
    {
        return new self('Legal_Portrait');
    }

    public static function Legal_Landscape() : self
    {
        return new self('Legal_Landscape');
    }

    public static function Slide_4_3() : self
    {
        return new self('Slide_4_3');
    }

    public static function Slide_16_9() : self
    {
        return new self('Slide_16_9');
    }

    public static function hydrate(string $size) : self
    {
        // Todo: Add size validation

        return new self($size);
    }

    public function size() : string
    {
        return $this->size;
    }
}
