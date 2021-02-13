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

namespace StructurizrPHP\Core\Documentation;

final class TemplateMetadata
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

    public static function hydrate(array $templateData) : self
    {
        return new self(
            $templateData['name'],
            $templateData['author'],
            $templateData['url'],
        );
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAuthor() : string
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author) : void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getUrl() : string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url) : void
    {
        $this->url = $url;
    }

    public function toArray() : array
    {
        return [
            'name' => $this->name,
            'author' => $this->author,
            'url' => $this->url,
        ];
    }
}
