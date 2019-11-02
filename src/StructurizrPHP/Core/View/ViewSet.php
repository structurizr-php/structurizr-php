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

namespace StructurizrPHP\StructurizrPHP\Core\View;

use StructurizrPHP\StructurizrPHP\Core\Model\SoftwareSystem;

final class ViewSet
{
    /**
     * @var SystemContextView[]
     */
    private $systemContextViews;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct()
    {
        $this->systemContextViews = [];
        $this->configuration = new Configuration();
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

    public function configuration(): Configuration
    {
        return $this->configuration;
    }

    public function toArray() : ?array
    {
        if (!\count($this->systemContextViews)) {
            return null;
        }

        return [
            'systemContextViews' => \array_map(
                function (SystemContextView $systemContextView) {
                    return $systemContextView->toArray();
                },
                $this->systemContextViews
            ),
            'configuration' => $this->configuration->toArray(),
        ];
    }
}
