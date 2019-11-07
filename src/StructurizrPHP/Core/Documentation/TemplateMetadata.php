<?php


namespace StructurizrPHP\StructurizrPHP\Core\Documentation;


class TemplateMetadata
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $author;
    /**
     * @var string
     */
    private $url;

    public function __construct(string $name, string $author, string $url)
{
    $this->name = $name;
    $this->author = $author;
    $this->url = $url;
}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

}