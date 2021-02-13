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

use StructurizrPHP\Core\Model\Element;

final class Section
{
    private $elementId;

    /**
     * @var Element
     */
    private $element;

    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $order;

    /**
     * @var Format
     */
    private $format;

    /**
     * @var string
     */
    private $content;

    public function __construct(Element $element, string $title, int $order, Format $format, string $content)
    {
        $this->element = $element;
        $this->title = $title;
        $this->order = $order;
        $this->format = $format;
        $this->content = $content;
    }

    public static function hydrate(array $sectionData, Element $element, Format $format) : self
    {
        return new self(
            $element,
            $sectionData['title'],
            $sectionData['order'],
            $format,
            $sectionData['content'],
        );
    }

    public function getElement() : Element
    {
        return $this->element;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getOrder() : int
    {
        return $this->order;
    }

    public function getFormat() : Format
    {
        return $this->format;
    }

    public function getContent() : string
    {
        return $this->content;
    }

    public function setOrder(int $order) : void
    {
        $this->order = $order;
    }

    public function setFormat(Format $format) : void
    {
        $this->format = $format;
    }

    public function setContent(string $content) : void
    {
        $this->content = $content;
    }

    public function setElement(Element $element) : void
    {
        $this->element = $element;
    }

    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }

    public function getElementId() : string
    {
        return $this->element->id();
    }

    public function equals(self $object) : bool
    {
        if ($this === $object) {
            return true;
        }

        return $this->getElementId() === $object->getElementId() && $this->getTitle() === $object->getTitle();
    }

    public function hashCode() : int
    {
        $result = $this->str_hashcode($this->getElementId());

        return 31 * $result + $this->str_hashcode($this->getTitle());
    }

    public function toArray() : array
    {
        return [
            'elementId' => $this->element->id(),
            'title' => $this->title,
            'order' => $this->order,
            'format' => $this->format->name(),
            'content' => $this->content,
        ];
    }

    private function str_hashcode(string $s) : int
    {
        $hash = 0;
        $len = \mb_strlen($s, 'UTF-8');

        if ($len === 0) {
            return $hash;
        }

        for ($i = 0; $i < $len; $i++) {
            $c = \mb_substr($s, $i, 1, 'UTF-8');
            $cc = \unpack('V', (string) \iconv('UTF-8', 'UCS-4LE', $c))[1];
            $hash = (($hash << 5) - $hash) + $cc;
            $hash &= $hash; // 16bit > 32bit
        }

        return $hash;
    }
}
