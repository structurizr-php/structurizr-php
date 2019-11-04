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

use StructurizrPHP\StructurizrPHP\Core\Model\SoftwareSystem;

final class SystemContextView extends StaticView
{
    /**
     * @var bool
     */
    private $enterpriseBoundaryVisible = true;

    public function __construct(SoftwareSystem $softwareSystem, string $title, string $description, string $key, ViewSet $viewSet)
    {
        parent::__construct($softwareSystem, $title, $description, $key, $viewSet);
    }

    public function addAllElements(): void
    {
        $this->addAllSoftwareSystems();
        $this->addAllPeople();
    }

    public function toArray() : array
    {
        return \array_merge(
            [
                'enterpriseBoundaryVisible' => $this->enterpriseBoundaryVisible,
            ],
            parent::toArray()
        );
    }

    /**
     * @psalm-suppress InvalidArgument
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedAssignment
     * @psalm-suppress ArgumentTypeCoercion
     */
    public static function hydrate(array $viewData, ViewSet $viewSet) : self
    {
        $view = new SystemContextView(
            $viewSet->model()->getElement($viewData['softwareSystemId']),
            $viewData['title'],
            $viewData['description'],
            $viewData['key'],
            $viewSet
        );

        if ($viewData['paperSize']) {
            $view->setPaperSize(PaperSize::hydrate($viewData['paperSize']));
        }

        foreach ($viewData['elements'] as $elementData) {
            $elementView = $view->addElement($viewSet->model()->getElement($elementData['id']), true);

            if (isset($viewData['x'], $viewData['y']) && $viewData['x'] && $viewData['y']) {
                $elementView->setX((int) $viewData['x'])->sety((int) $viewData['y']);
            }
        }

        if ($viewData['automaticLayout']) {
            $view->automaticLayout = AutomaticLayout::hydrate($viewData['automaticLayout']);
        }

        return $view;
    }
}
