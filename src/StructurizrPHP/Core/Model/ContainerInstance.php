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

namespace StructurizrPHP\Core\Model;

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
     * @var HttpHealthCheck[]
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

    public static function hydrate(array $containerInstanceData, Model $model) : self
    {
        $element = $model->getElement($containerInstanceData['containerId']);

        $instance = new self(
            $element instanceof Container ? $element : null,
            $containerInstanceData['instanceId'],
            $containerInstanceData['environment'],
            $containerInstanceData['id'],
            $model
        );

        if (isset($containerInstanceData['healthChecks'])) {
            if (\is_array($containerInstanceData['healthChecks'])) {
                foreach ($containerInstanceData['healthChecks'] as $healthCheckData) {
                    $instance->healthChecks[] = HttpHealthCheck::hydrate($healthCheckData);
                }
            }
        }

        parent::hydrateDeploymentElement($instance, $containerInstanceData);

        return $instance;
    }

    public function getContainer() : Container
    {
        return $this->container;
    }

    public function getParent() : ?Element
    {
        return $this->container->getParent();
    }

    public function getCanonicalName() : string
    {
        return $this->container->getCanonicalName() . '[' . $this->instanceId . ']';
    }

    /**
     * @return HttpHealthCheck[]
     */
    public function getHealthChecks() : array
    {
        return $this->healthChecks;
    }

    public function addHealthCheck(string $name, string $url, ?int $interval = null, ?int $timeout = null) : HttpHealthCheck
    {
        $healthCheck = new HttpHealthCheck(
            $name,
            $url,
            $interval ? $interval : self::DEFAULT_HEALTH_CHECK_INTERVAL_IN_SECONDS,
            $timeout ? $timeout : self::DEFAULT_HEALTH_CHECK_TIMEOUT_IN_MILLISECONDS
        );

        $this->healthChecks[] = $healthCheck;

        return $healthCheck;
    }

    public function toArray() : array
    {
        $data = \array_merge(
            [
                'containerId' => $this->container->id(),
                'instanceId' => $this->instanceId,
            ],
            parent::toArray()
        );

        if (\count($this->healthChecks)) {
            $data['healthChecks'] = \array_map(
                function (HttpHealthCheck $healthCheck) {
                    return $healthCheck->toArray();
                },
                $this->healthChecks
            );
        }

        return $data;
    }
}
