<?php


namespace StructurizrPHP\StructurizrPHP\Core\View;


use Assert\Assert;
use StructurizrPHP\StructurizrPHP\Assertion;

class Branding
{
    private $logo;
    /**
     * @var Font
     */
    private $font;

    public function setLogo(string $url)
    {
        Assertion::startsWith($url, 'data:image/');
        $this->logo = $url;
    }

    public function toArray(): array
    {
        return [
            'logo' => $this->logo
        ];
    }
}