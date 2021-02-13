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

use StructurizrPHP\Core\Assertion;
use StructurizrPHP\Core\Model\Container;
use StructurizrPHP\Core\Model\Model;
use StructurizrPHP\Core\Model\SoftwareSystem;

final class ViewSet
{
    /**
     * @var SystemContextView[]
     */
    private $systemContextViews;

    /**
     * @var SystemLandscapeView[]
     */
    private $systemLandscapeViews;

    /**
     * @var DynamicView[]
     */
    private $dynamicViews;

    /**
     * @var DeploymentView[]
     */
    private $deploymentViews;

    /**
     * @var ContainerView[]
     */
    private $containerViews;

    /**
     * @var ComponentView[]
     */
    private $componentViews;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Model
     */
    private $model;

    public function __construct(Model $model)
    {
        $this->systemContextViews = [];
        $this->systemLandscapeViews = [];
        $this->dynamicViews = [];
        $this->deploymentViews = [];
        $this->containerViews = [];
        $this->componentViews = [];
        $this->configuration = new Configuration();
        $this->model = $model;
    }

    public static function hydrate(?array $viewSetData, Model $model) : self
    {
        $viewSet = new self($model);

        if (!$viewSetData) {
            return $viewSet;
        }

        $viewSetDataModel = new ViewSetDataObject($viewSetData);

        if ($viewSetDataModel->hasViews('systemLandscapeViews')) {
            $viewSet->systemLandscapeViews = $viewSetDataModel->map(
                'systemLandscapeViews',
                function (array $viewData) use ($viewSet) {
                    return SystemLandscapeView::hydrate($viewData, $viewSet);
                }
            );
        }

        if ($viewSetDataModel->hasViews('containerViews')) {
            $viewSet->containerViews = $viewSetDataModel->map(
                'containerViews',
                function (array $viewData) use ($viewSet) {
                    return ContainerView::hydrate($viewData, $viewSet);
                }
            );
        }

        if ($viewSetDataModel->hasViews('componentViews')) {
            $viewSet->componentViews = $viewSetDataModel->map(
                'componentViews',
                function (array $viewData) use ($viewSet) {
                    return ComponentView::hydrate($viewData, $viewSet);
                }
            );
        }

        if ($viewSetDataModel->hasViews('systemContextViews')) {
            $viewSet->systemContextViews = $viewSetDataModel->map(
                'systemContextViews',
                function (array $viewData) use ($viewSet) {
                    return SystemContextView::hydrate($viewData, $viewSet);
                }
            );
        }

        if ($viewSetDataModel->hasViews('dynamicViews')) {
            $viewSet->dynamicViews = $viewSetDataModel->map(
                'dynamicViews',
                function (array $viewData) use ($viewSet) {
                    return DynamicView::hydrate($viewData, $viewSet);
                }
            );
        }

        if ($viewSetDataModel->hasViews('deploymentViews')) {
            $viewSet->deploymentViews = $viewSetDataModel->map(
                'deploymentViews',
                function (array $viewData) use ($viewSet) {
                    return DeploymentView::hydrate($viewData, $viewSet);
                }
            );
        }

        $viewSet->configuration = Configuration::hydrate($viewSetData['configuration']);

        return $viewSet;
    }

    public function getModel() : Model
    {
        return $this->model;
    }

    public function createSystemContextView(
        SoftwareSystem $softwareSystem,
        string $key,
        string $description
    ) : SystemContextView {
        $view = new SystemContextView(
            $softwareSystem,
            $description,
            $key,
            $this
        );

        $this->systemContextViews[] = $view;

        return $view;
    }

    public function createSystemLandscapeView(string $key, string $description) : SystemLandscapeView
    {
        $view = new SystemLandscapeView(
            $this->model,
            $description,
            $key,
            $this
        );

        $this->systemLandscapeViews[] = $view;

        return $view;
    }

    /**
     * Creates a dynamic view, where the scope is the specified software system. The following
     * elements can be added to the resulting view:.
     *
     * <ul>
     * <li>People</li>
     * <li>Software systems</li>
     * <li>Containers that reside inside the specified software system</li>
     * </ul>
     */
    public function createDynamicView(SoftwareSystem $softwareSystem, string $key, string $description) : DynamicView
    {
        $view = DynamicView::softwareSystem($softwareSystem, $key, $description, $this);

        $this->dynamicViews[] = $view;

        return $view;
    }

    /**
     * Creates a dynamic view, where the scope is the specified container. The following
     * elements can be added to the resulting view:.
     *
     * <ul>
     * <li>People</li>
     * <li>Software systems</li>
     * <li>Containers with the same parent software system as the specified container</li>
     * <li>Components within the specified container</li>
     * </ul>
     */
    public function createContainerDynamicView(Container $container, string $key, string $description) : DynamicView
    {
        $view = DynamicView::container($container, $key, $description, $this);

        $this->dynamicViews[] = $view;

        return $view;
    }

    public function createContainerView(SoftwareSystem $softwareSystem, string $key, string $description) : ContainerView
    {
        $view = new ContainerView($softwareSystem, $key, $description, $this);

        $this->containerViews[] = $view;

        return $view;
    }

    public function createComponentView(Container $container, string $key, string $description) : ComponentView
    {
        $view = new ComponentView($container, $key, $description, $this);

        $this->componentViews[] = $view;

        return $view;
    }

    public function createDeploymentView(SoftwareSystem $softwareSystem, string $key, string $description) : DeploymentView
    {
        $view = new DeploymentView($softwareSystem, $description, $key, $this);
        $this->deploymentViews[] = $view;

        return $view;
    }

    public function getConfiguration() : Configuration
    {
        return $this->configuration;
    }

    public function copyLayoutInformationFrom(self $source) : void
    {
        foreach ($this->systemContextViews as $contextView) {
            $sourceSystemContextView = \current(\array_filter(
                $source->systemContextViews,
                function (SystemContextView $nextSystemContextView) use ($contextView) {
                    return $nextSystemContextView->keyEquals($contextView);
                }
            ));

            if ($sourceSystemContextView) {
                $contextView->copyLayoutInformationFrom($sourceSystemContextView);
            }
        }

        foreach ($this->systemLandscapeViews as $landscapeView) {
            $sourceLandscapeView = \current(\array_filter(
                $source->systemLandscapeViews,
                function (SystemLandscapeView $nextLandscapeView) use ($landscapeView) {
                    return $nextLandscapeView->keyEquals($landscapeView);
                }
            ));

            if ($sourceLandscapeView) {
                $landscapeView->copyLayoutInformationFrom($sourceLandscapeView);
            }
        }

        foreach ($this->dynamicViews as $dynamicView) {
            $sourceDynamicView = \current(\array_filter(
                $source->dynamicViews,
                function (DynamicView $nextDynamicView) use ($dynamicView) {
                    return $nextDynamicView->keyEquals($dynamicView);
                }
            ));

            if ($sourceDynamicView) {
                $dynamicView->copyLayoutInformationFrom($sourceDynamicView);
            }
        }

        foreach ($this->containerViews as $containerView) {
            $sourceContainerView = \current(\array_filter(
                $source->containerViews,
                function (ContainerView $nextContainerView) use ($containerView) {
                    return $nextContainerView->keyEquals($containerView);
                }
            ));

            if ($sourceContainerView) {
                $containerView->copyLayoutInformationFrom($sourceContainerView);
            }
        }

        foreach ($this->componentViews as $componentView) {
            $sourceComponentView = \current(\array_filter(
                $source->componentViews,
                function (ComponentView $nextComponentView) use ($componentView) {
                    return $nextComponentView->keyEquals($componentView);
                }
            ));

            if ($sourceComponentView) {
                $componentView->copyLayoutInformationFrom($sourceComponentView);
            }
        }

        foreach ($this->deploymentViews as $deploymentView) {
            $sourceDeploymentView = \current(\array_filter(
                $source->deploymentViews,
                function (DeploymentView $nextDeploymentView) use ($deploymentView) {
                    return $nextDeploymentView->keyEquals($deploymentView);
                }
            ));

            if ($sourceDeploymentView) {
                $deploymentView->copyLayoutInformationFrom($sourceDeploymentView);
            }
        }
    }

    public function toArray() : ?array
    {
        if (
            !\count($this->systemContextViews)
            && !\count($this->systemLandscapeViews)
            && !\count($this->dynamicViews)
            && !\count($this->deploymentViews)
            && !\count($this->containerViews)
            && !\count($this->componentViews)
        ) {
            return null;
        }

        $data = [
            'configuration' => $this->configuration->toArray(),
        ];

        if (\count($this->systemContextViews)) {
            $data['systemContextViews'] = \array_map(
                function (SystemContextView $systemContextView) {
                    return $systemContextView->toArray();
                },
                $this->systemContextViews
            );
        }

        if (\count($this->systemLandscapeViews)) {
            $data['systemLandscapeViews'] = \array_map(
                function (SystemLandscapeView $systemLandscapeView) {
                    return $systemLandscapeView->toArray();
                },
                $this->systemLandscapeViews
            );
        }

        if (\count($this->containerViews)) {
            $data['containerViews'] = \array_map(
                function (ContainerView $containerView) {
                    return $containerView->toArray();
                },
                $this->containerViews
            );
        }

        if (\count($this->componentViews)) {
            $data['componentViews'] = \array_map(
                function (ComponentView $componentView) {
                    return $componentView->toArray();
                },
                $this->componentViews
            );
        }

        if (\count($this->dynamicViews)) {
            $data['dynamicViews'] = \array_map(
                function (DynamicView $dynamicView) {
                    return $dynamicView->toArray();
                },
                $this->dynamicViews
            );
        }

        if (\count($this->deploymentViews)) {
            $data['deploymentViews'] = \array_map(
                function (DeploymentView $deploymentView) {
                    return $deploymentView->toArray();
                },
                $this->deploymentViews
            );
        }

        return $data;
    }
}

final class ViewSetDataObject
{
    /**
     * @var array
     */
    private $viewSetData;

    public function __construct(array $viewSetData)
    {
        $this->viewSetData = $viewSetData;
    }

    public function map(string $type, callable $callback) : array
    {
        return \array_map($callback, $this->viewSetData[$type]);
    }

    public function hasViews(string $name) : bool
    {
        Assertion::inArray($name, ['systemLandscapeViews', 'systemContextViews', 'dynamicViews', 'deploymentViews', 'containerViews', 'componentViews']);

        return \array_key_exists($name, $this->viewSetData) && \is_array($this->viewSetData[$name]);
    }
}
