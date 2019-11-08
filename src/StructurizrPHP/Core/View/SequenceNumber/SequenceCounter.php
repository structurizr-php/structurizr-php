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

namespace StructurizrPHP\StructurizrPHP\Core\View\SequenceNumber;

class SequenceCounter
{
    /**
     * @var int
     */
    private $sequence;

    /**
     * @var SequenceCounter|null
     */
    private $parent;

    public function __construct(?SequenceCounter $parent = null)
    {
        $this->parent = $parent;
        $this->sequence = 0;
    }

    public function increment() : void
    {
        $this->sequence++;
    }

    public function getParent(): ?SequenceCounter
    {
        return $this->parent;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function setSequence(int $sequence): void
    {
        $this->sequence = $sequence;
    }

    public function __toString()
    {
        return (string) $this->sequence;
    }
}
