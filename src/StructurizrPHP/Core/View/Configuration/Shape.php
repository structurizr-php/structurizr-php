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

namespace StructurizrPHP\Core\View\Configuration;

final class Shape
{
    private $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function person() : self
    {
        return new self('Person');
    }

    public static function box() : self
    {
        return new self('Box');
    }

    public static function roundedBox() : self
    {
        return new self('RoundedBox');
    }

    public static function circle() : self
    {
        return new self('Circle');
    }

    public static function ellipse() : self
    {
        return new self('Ellipse');
    }

    public static function hexagon() : self
    {
        return new self('Hexagon');
    }

    public static function folder() : self
    {
        return new self('Folder');
    }

    public static function cylinder() : self
    {
        return new self('Cylinder');
    }

    public static function pipe() : self
    {
        return new self('Pipe');
    }

    public static function robot() : self
    {
        return new self('Robot');
    }

    public static function webBrowser() : self
    {
        return new self('WebBrowser');
    }

    public static function mobileDevicePortrait() : self
    {
        return new self('MobileDevicePortrait');
    }

    public static function mobileDeviceLandscape() : self
    {
        return new self('MobileDeviceLandscape');
    }

    public static function hydrate(string $name) : self
    {
        return new self($name);
    }

    public function name() : string
    {
        return $this->name;
    }
}
