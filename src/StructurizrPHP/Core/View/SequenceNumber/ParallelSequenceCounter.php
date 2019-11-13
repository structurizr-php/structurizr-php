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

namespace StructurizrPHP\Core\View\SequenceNumber;

final class ParallelSequenceCounter extends SequenceCounter
{
    public function __construct(SequenceCounter $parent)
    {
        parent::__construct($parent);
        $this->setSequence($parent->getSequence());
    }
}
