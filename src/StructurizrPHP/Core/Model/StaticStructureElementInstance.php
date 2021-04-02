<?php

declare(strict_types=1);

namespace StructurizrPHP\Core\Model;

use StructurizrPHP\Core\Model\Relationship\InteractionStyle;

abstract class StaticStructureElementInstance extends DeploymentElement
{
    protected const DEFAULT_HEALTH_CHECK_INTERVAL_IN_SECONDS = 60;

    protected const DEFAULT_HEALTH_CHECK_TIMEOUT_IN_MILLISECONDS = 0;

    /**
     * @var int
     */
    protected $instanceId;

    /**
     * @var HttpHealthCheck[]
     */
    protected $healthChecks;

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

    public function usesInfrastructureNode(InfrastructureNode $infrastructureNode, string $description = 'Uses', string $technology = null, InteractionStyle $interactionStyle = null) : Relationship
    {
        return $this->getModel()->addRelationship(
            $this,
            $infrastructureNode,
            $description,
            $technology,
            $interactionStyle
        );
    }
}
