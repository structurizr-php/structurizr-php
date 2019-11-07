<?php


namespace StructurizrPHP\StructurizrPHP\Core\Util;


use StructurizrPHP\StructurizrPHP\Assertion;

class ImageUtils
{
    public static function getImageAsDataUri(string $imagePath): string
    {
        Assertion::file($imagePath);
        $type = pathinfo($imagePath, PATHINFO_EXTENSION);
        $data = file_get_contents($imagePath);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}