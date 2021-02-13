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

final class AutomaticLayout
{
    /**
     * @var RankDirection
     */
    private $rankDirection;

    /**
     * @var int
     */
    private $rankSeparation;

    /**
     * @var int
     */
    private $nodeSeparation;

    /**
     * @var int
     */
    private $edgeSeparation;

    /**
     * @var bool
     */
    private $vertices;

    public function __construct(
        RankDirection $rankDirection,
        int $rankSeparation,
        int $nodeSeparation,
        int $edgeSeparation,
        bool $vertices
    ) {
        $this->rankDirection = $rankDirection;
        $this->rankSeparation = $rankSeparation;
        $this->nodeSeparation = $nodeSeparation;
        $this->edgeSeparation = $edgeSeparation;
        $this->vertices = $vertices;
    }

    public static function hydrate(array $automaticLayoutData) : self
    {
        return new self(
            RankDirection::hydrate($automaticLayoutData['rankDirection']),
            (int) $automaticLayoutData['rankSeparation'],
            (int) $automaticLayoutData['nodeSeparation'],
            (int) $automaticLayoutData['edgeSeparation'],
            $automaticLayoutData['vertices'],
        );
    }

    public function toArray() : array
    {
        return [
            'rankDirection' => $this->rankDirection->direction(),
            'rankSeparation' => $this->rankSeparation,
            'nodeSeparation' => $this->nodeSeparation,
            'edgeSeparation' => $this->edgeSeparation,
            'vertices' => $this->vertices,
        ];
    }
}
