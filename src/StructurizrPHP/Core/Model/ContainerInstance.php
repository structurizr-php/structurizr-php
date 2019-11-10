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

namespace StructurizrPHP\StructurizrPHP\Core\Model;

/**
 * Represents a deployment instance of a {@link Container}, which can be added to a {@link DeploymentNode}.
 */
final class ContainerInstance extends DeploymentElement
{
    private const DEFAULT_HEALTH_CHECK_INTERVAL_IN_SECONDS = 60;
    private const DEFAULT_HEALTH_CHECK_TIMEOUT_IN_MILLISECONDS = 0;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var int
     */
    private $instanceId;

    /**
     * @var array
     */
    private $healthChecks;

    public function __construct(Container $container, int $instanceId, string $environment, string $id, Model $model)
    {
        parent::__construct($id, $model);
        $this->setEnvironment($environment);
        $this->container = $container;
        $this->instanceId = $instanceId;
        $this->healthChecks = [];
        $this->addTags(Tags::CONTAINER_INSTANCE);
    }

    public function getContainer() : Container
    {
        return $this->container;
    }

    public function getCanonicalName() : string
    {
        return $this->container->getCanonicalName() . "[" . $this->instanceId . "]";
    }

    public function toArray() : array
    {
        $data = \array_merge(
            [
                'containerId' => $this->container->id(),
                'instanceId' => $this->instanceId,
                'healthChecks' => [],
            ],
            parent::toArray()
        );

        return $data;
    }

    public static function hydrate(array $containerInstanceData, Model $model) : self
    {
        $instance = new self(
            $model->getElement($containerInstanceData['containerId']),
            $containerInstanceData['instanceId'],
            $containerInstanceData['environment'],
            $containerInstanceData['id'],
            $model
        );

        parent::hydrateDeploymentElement($instance, $containerInstanceData);

        return $instance;
    }
}
