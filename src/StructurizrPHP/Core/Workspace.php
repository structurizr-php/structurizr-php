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

namespace StructurizrPHP\Core;

use StructurizrPHP\Core\Documentation\Documentation;
use StructurizrPHP\Core\Model\Model;
use StructurizrPHP\Core\View\ViewSet;

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
     * @var ViewSet
     */
    private $viewSet;

    /**
     * @var Documentation
     */
    private $documentation;

    public function __construct(string $id, string $name, string $description)
    {
        Assertion::integerish($id);
        Assertion::notEmpty($name);
        Assertion::notEmpty($description);

        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->model = new Model();
        $this->viewSet = new ViewSet($this->model);
        $this->documentation = new Documentation($this->model);
    }

    public static function hydrate(array $workspaceData) : self
    {
        $workspace = new self(
            (string) $workspaceData['id'],
            $workspaceData['name'],
            $workspaceData['description']
        );

        $workspace->model = Model::hydrate((array) $workspaceData['model']);
        $workspace->viewSet = ViewSet::hydrate((array) $workspaceData['views'], $workspace->model);

        if (isset($workspaceData['documentation'])) {
            $workspace->documentation = Documentation::hydrate((array) $workspaceData['documentation'], $workspace->model);
        }

        return $workspace;
    }

    public function id() : string
    {
        return $this->id;
    }

    public function getModel() : Model
    {
        return $this->model;
    }

    public function getViews() : ViewSet
    {
        return $this->viewSet;
    }

    public function toArray(string $agentName) : array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'lastModifiedDate' => (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('c'),
            'lastModifiedAgent' => $agentName,
            'model' => $this->model->toArray(),
            'views' => $this->viewSet->toArray(),
        ];

        if (!$this->documentation->isEmpty()) {
            $data['documentation'] = $this->documentation->toArray();
        }

        return $data;
    }

    public function getDocumentation() : Documentation
    {
        return  $this->documentation;
    }
}
