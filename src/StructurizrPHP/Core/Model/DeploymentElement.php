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

abstract class DeploymentElement extends Element
{
    public const DEFAULT_DEPLOYMENT_ENVIRONMENT = 'Deafault';

    /**
     * @var string
     */
    private $environment = self::DEFAULT_DEPLOYMENT_ENVIRONMENT;

    public static function hydrateDeploymentElement(self $element, array $elementData) : void
    {
        $element->setEnvironment($elementData['environment']);

        parent::hydrateElement($element, $elementData);
    }

    public function getEnvironment() : string
    {
        return $this->environment;
    }

    public function setEnvironment(string $environment) : void
    {
        $this->environment = $environment;
    }

    public function toArray() : array
    {
        return \array_merge(
            ['environment' => $this->environment],
            parent::toArray()
        );
    }
}
