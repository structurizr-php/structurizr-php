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

namespace StructurizrPHP\Core\Util;

use StructurizrPHP\Core\Assertion;

final class ImageUtils
{
    public static function getImageAsDataUri(string $imagePath) : string
    {
        Assertion::file($imagePath);
        $type = \pathinfo($imagePath, PATHINFO_EXTENSION);
        $data = \file_get_contents($imagePath);

        return 'data:image/' . $type . ';base64,' . \base64_encode((string) $data);
    }
}
