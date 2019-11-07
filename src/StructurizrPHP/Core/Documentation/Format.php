<?php


namespace StructurizrPHP\StructurizrPHP\Core\Documentation;


class Format
{
    private $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public static function markdown(): self
    {
        return new self('Markdown');
    }

    public static function asciiDoc(): self
    {
        return new self('AsciiDoc');
    }
}