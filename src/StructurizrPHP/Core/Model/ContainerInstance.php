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
final class ContainerInstance extends StaticStructureElementInstance
{
    /**
     * @var Container
     */
    private $container;

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
                    $instance->healthChecks[] = HttpHealthCheck::hydrate(
                        $healthCheckData
                    );
                }
            }
        }

        parent::hydrateDeploymentElement($instance, $containerInstanceData, $model);

        return $instance;
    }

    public function getContainer() : Container
    {
        return $this->container;
    }

    public function getCanonicalName() : string
    {
        return $this->container->getCanonicalName() . '[' . $this->instanceId . ']';
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
