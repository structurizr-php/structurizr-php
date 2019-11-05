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

namespace StructurizrPHP\StructurizrPHP\Core\View;

use StructurizrPHP\StructurizrPHP\Assertion;
use StructurizrPHP\StructurizrPHP\Core\Model\Model;
use StructurizrPHP\StructurizrPHP\Core\Model\SoftwareSystem;

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
        $this->configuration = new Configuration();
        $this->model = $model;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function createSystemContextView(
        SoftwareSystem $softwareSystem,
        string $title,
        string $key,
        string $description
    ) : SystemContextView {
        $view = new SystemContextView(
            $softwareSystem,
            $title,
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

        $this->systemLandscapeViews [] = $view;

        return $view;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function copyLayoutInformationFrom(ViewSet $source) : void
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
    }

    public function toArray() : ?array
    {
        if (!\count($this->systemContextViews) && !\count($this->systemLandscapeViews)) {
            return null;
        }

        $data = [
            'configuration' => $this->configuration->toArray(),
        ];

        if (\count($this->systemContextViews)) {
            $data = \array_merge(
                $data,
                [
                    'systemContextViews' => \array_map(
                        function (SystemContextView $systemContextView) {
                            return $systemContextView->toArray();
                        },
                        $this->systemContextViews
                    ),
                ]
            );
        }

        if (\count($this->systemLandscapeViews)) {
            $data = \array_merge(
                $data,
                [
                    'systemLandscapeViews' => \array_map(
                        function (SystemLandscapeView $systemLandscapeView) {
                            return $systemLandscapeView->toArray();
                        },
                        $this->systemLandscapeViews
                    ),
                ]
            );
        }

        return $data;
    }

    /**
     * @psalm-suppress InvalidArgument
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedPropertyTypeCoercion
     */
    public static function hydrate(?array $viewSetData, Model $model) : self
    {
        $viewSet = new self($model);

        if (!$viewSetData) {
            return $viewSet;
        }

        $viewSetDataModel = new ViewSetDataModel($viewSetData);

        if ($viewSetDataModel->hasViews('systemLandscapeViews')) {
            $viewSet->systemLandscapeViews = $viewSetDataModel->mapLandscapeViews(
                function (array $viewData) use ($viewSet) {
                    return SystemLandscapeView::hydrate($viewData, $viewSet);
                }
            );
        }

        if ($viewSetDataModel->hasViews('systemContextViews')) {
            $viewSet->systemContextViews = $viewSetDataModel->mapSystemContextViews(
                function (array $viewData) use ($viewSet) {
                    return SystemContextView::hydrate($viewData, $viewSet);
                }
            );
        }

        $viewSet->configuration = Configuration::hydrate($viewSetData['configuration']);

        return $viewSet;
    }
}

final class ViewSetDataModel
{
    /**
     * @var array
     */
    private $viewSetData;

    public function __construct(array $viewSetData)
    {
        $this->viewSetData = $viewSetData;
    }

    /**
     * @psalm-suppress InvalidArgument
     * @psalm-suppress MixedArgument
     */
    public function mapLandscapeViews(callable $callback) : array
    {
        return \array_map($callback, $this->viewSetData['systemLandscapeViews']);
    }

    /**
     * @psalm-suppress InvalidArgument
     * @psalm-suppress MixedArgument
     */
    public function mapSystemContextViews(callable $callback) : array
    {
        return \array_map($callback, $this->viewSetData['systemContextViews']);
    }


    public function hasViews(string $name) : bool
    {
        Assertion::inArray($name, ['systemLandscapeViews', 'systemContextViews']);

        return \array_key_exists($name, $this->viewSetData) && \is_array($this->viewSetData[$name]);
    }
}
