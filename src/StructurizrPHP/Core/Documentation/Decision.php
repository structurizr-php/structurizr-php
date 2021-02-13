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

final class Decision
{
    /**
     * @var string
     */
    private $elementId;

    /**
     * @var Element
     */
    private $element;

    /**
     * @var string
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     */
    private $date;

    /**
     * @var string
     */
    private $title;

    /**
     * @var DecisionStatus
     */
    private $status;

    /**
     * @var Format
     */
    private $format;

    /**
     * @var string
     */
    private $content;

    public function __construct(
        Element $element,
        string $id,
        \DateTimeImmutable $date,
        string $title,
        DecisionStatus $status,
        Format $format,
        string $content
    ) {
        $this->element = $element;
        $this->id = $id;
        $this->date = $date;
        $this->title = $title;
        $this->status = $status;
        $this->format = $format;
        $this->content = $content;
    }

    public static function hydrate(array $decisionData, Element $element) : self
    {
        return new self(
            $element,
            $decisionData['id'],
            new \DateTimeImmutable($decisionData['date']),
            $decisionData['title'],
            DecisionStatus::hydrate($decisionData['status']),
            Format::hydrate($decisionData['format']),
            $decisionData['content'],
        );
    }

    /**
     * @return string
     */
    public function getElementId() : string
    {
        return $this->elementId;
    }

    /**
     * @return Element
     */
    public function getElement() : Element
    {
        return $this->element;
    }

    /**
     * @param Element $element
     */
    public function setElement(Element $element) : void
    {
        $this->element = $element;
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id) : void
    {
        $this->id = $id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate() : \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @param \DateTimeImmutable $date
     */
    public function setDate(\DateTimeImmutable $date) : void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }

    /**
     * @return DecisionStatus
     */
    public function getStatus() : DecisionStatus
    {
        return $this->status;
    }

    /**
     * @param DecisionStatus $status
     */
    public function setStatus(DecisionStatus $status) : void
    {
        $this->status = $status;
    }

    /**
     * @return Format
     */
    public function getFormat() : Format
    {
        return $this->format;
    }

    /**
     * @param Format $format
     */
    public function setFormat(Format $format) : void
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getContent() : string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content) : void
    {
        $this->content = $content;
    }

    public function toArray() : array
    {
        return [
            'elementId' => $this->element->id(),
            'id' => $this->id,
            'date' => $this->date->format(\DateTime::ATOM),
            'title' => $this->title,
            'status' => (string) $this->status,
            'content' => $this->content,
            'format' => $this->format->name(),
        ];
    }
}
