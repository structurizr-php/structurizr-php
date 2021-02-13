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

namespace StructurizrPHP\Core\View;

use StructurizrPHP\Core\Exception\RuntimeException;
use StructurizrPHP\Core\View\SequenceNumber\ParallelSequenceCounter;

final class SequenceNumber
{
    /**
     * @var SequenceNumber\SequenceCounter
     */
    private $counter;

    public function __construct()
    {
        $this->counter = new SequenceNumber\SequenceCounter();
    }

    public function getNext() : string
    {
        $this->counter->increment();

        return (string) $this->counter;
    }

    public function startParallelSequence() : void
    {
        $this->counter = new ParallelSequenceCounter($this->counter);
    }

    public function endParallelSequence(bool $endAllParallelSequencesAndContinueNumbering) : void
    {
        $parentSequence = $this->counter->getParent();

        if (!$parentSequence) {
            throw new RuntimeException('Parallel Sequence never started');
        }

        if ($endAllParallelSequencesAndContinueNumbering) {
            $sequence = $this->counter->getSequence();
            $this->counter = $parentSequence;
            $this->counter->setSequence($sequence);
        } else {
            $this->counter = $parentSequence;
        }
    }
}
