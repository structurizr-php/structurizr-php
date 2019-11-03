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

use StructurizrPHP\StructurizrPHP\Core\View\Configuration\Styles;

final class Configuration
{
    /**
     * @var Styles
     */
    private $styles;

    public function __construct()
    {
        $this->styles = new Styles();
    }

    public function getStyles(): Styles
    {
        return $this->styles;
    }

    public function toArray() : array
    {
        return [
            'styles' => $this->styles->toArray(),
        ];
    }
}
