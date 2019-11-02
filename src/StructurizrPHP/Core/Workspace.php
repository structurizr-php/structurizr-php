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

namespace StructurizrPHP\StructurizrPHP\Core;

use StructurizrPHP\StructurizrPHP\Assertion;
use StructurizrPHP\StructurizrPHP\Core\Model\Model;
use StructurizrPHP\StructurizrPHP\Core\View\ViewSet;

final class Workspace
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var \StructurizrPHP\StructurizrPHP\Core\View\ViewSet
     */
    private $viewSet;

    public function __construct(string $id, string $name, string $description)
    {
        Assertion::integerish($id);
        Assertion::notEmpty($name);
        Assertion::notEmpty($description);

        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->model = new Model();
        $this->viewSet = new ViewSet();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function viewSet(): ViewSet
    {
        return $this->viewSet;
    }

    public function toArray(string $agentName) : array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'lastModifiedDate' => (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('c'),
            'lastModifiedAgent' => $agentName,
            'model' => $this->model->toArray(),
            'views' => $this->viewSet->toArray(),
            'documentation' => null,
            'configuration' => null,
        ];
    }
}
