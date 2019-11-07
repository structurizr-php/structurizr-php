<?php


namespace StructurizrPHP\Tests\StructurizrPHP\Tests\Unit\Core;


use PHPUnit\Framework\TestCase;
use StructurizrPHP\StructurizrPHP\Core\Workspace;

class AbstractWorkspaceTestBase extends TestCase
{
    /**
     * @var Workspace
     */
    protected $workspace;
    /**
     * @var string
     */
    protected $model;
    /**
     * @var string
     */
    protected $views;

    protected function setUp(): void
    {
        $this->workspace = new Workspace(1, "Name", "Description");
        $this->model = $this->workspace->getModel();
        $this->views = $this->workspace->getViews();
    }

}