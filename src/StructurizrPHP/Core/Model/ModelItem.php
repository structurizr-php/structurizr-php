<?php

declare(strict_types=1);

/*
 * This file is part of the Structurizr SDK for PHP.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StructurizrPHP\StructurizrPHP\Core\Model;

use StructurizrPHP\StructurizrPHP\Assertion;

abstract class ModelItem
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Tags
     */
    private $tags;

    /**
     * @var Properties
     */
    private $properties;

    public function __construct(string $id)
    {
        Assertion::integerish($id);

        $this->id = $id;
        $this->tags = new Tags();
        $this->properties = new Properties();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function setTags(Tags $tags): void
    {
        $this->tags = $tags;
    }

    public function setProperties(Properties $properties): void
    {
        $this->properties = $properties;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->id,
            'tags' => $this->tags->toArray(),
            'properties' => $this->properties->toArray(),
        ];
    }
}
