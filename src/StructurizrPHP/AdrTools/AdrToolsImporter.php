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

namespace StructurizrPHP\AdrTools;

use StructurizrPHP\Core\Assertion;
use StructurizrPHP\Core\Documentation\Decision;
use StructurizrPHP\Core\Documentation\DecisionStatus;
use StructurizrPHP\Core\Documentation\Format;
use StructurizrPHP\Core\Model\SoftwareSystem;
use StructurizrPHP\Core\Workspace;

final class AdrToolsImporter
{
    /**
     * @var Workspace
     */
    private $workspace;

    /**
     * @var string
     */
    private $path;

    public function __construct(Workspace $workspace, string $path)
    {
        Assertion::directory($path);

        $this->workspace = $workspace;
        $this->path = $path;
    }

    /**
     * @param SoftwareSystem $softwareSystem
     * @return Decision[]
     */
    public function importArchitectureDecisionRecords(SoftwareSystem $softwareSystem) : array
    {
        $decisions = [];
        /** @var string[] $markdownFiles */
        $markdownFiles = array_map(function ($i) {
            return (string)$i;
        }, (array)scandir($this->path));
        $markdownFiles = array_filter($markdownFiles, function ($file) {
            return (substr($file, -3) === '.md');
        });

        if (!empty($markdownFiles)) {
            // first create an index of filename -> ID
            $index = [];
            foreach ($markdownFiles as $file) {
                $index[$file] = $this->extractIntegerIdFromFileName($file);
            }

            foreach ($markdownFiles as $file) {
                $id = $this->extractIntegerIdFromFileName($file);
                $content = (string)file_get_contents($this->path . DIRECTORY_SEPARATOR . $file);
                $content = str_replace("\t", '', $content);
                $format = Format::markdown();
                $title = $this->extractTitle($content);
                $date = $this->extractDate($content);
                $status = $this->extractStatus($content);
                foreach ($index as $filename => $fileId) {
                    $content = str_replace($filename, $this->calculateUrl($fileId, $softwareSystem), $content);
                }
                $decision = $this->workspace->getDocumentation()->addDecision($softwareSystem, $id, $date, $title, $status, $format, $content);
                $decisions[] = $decision;
            }
        }

        return $decisions;
    }

    private function extractIntegerIdFromFileName(string $file) : string
    {
        return substr($file, 0, 4);
    }

    private function extractTitle(string $content) : string
    {
        if (preg_match('/^# \\d*\\. (.*)$/m', $content, $matches)) {
            return $matches[1];
        }

        return 'Untitled';
    }

    private function extractDate(string $content) : \DateTimeImmutable
    {
        if (preg_match('/^Date: (\d\d\d\d-\d\d-\d\d)$/m', $content, $matches) && $date = \DateTimeImmutable::createFromFormat('Y-m-d', $matches[1])) {
            return $date;
        }

        return new \DateTimeImmutable();
    }

    private function extractStatus(string $content) : DecisionStatus
    {
        if (preg_match('/## Status\n\n(\w*)/', $content, $matches)) {
            return DecisionStatus::hydrate($matches[1]);
        }

        return DecisionStatus::proposed();
    }

    private function calculateUrl(string $fileId, SoftwareSystem $softwareSystem = null) : string
    {
        if ($softwareSystem === null) {
            return '#/:' . urldecode($fileId);
        }

        return '#' . urldecode($softwareSystem->getCanonicalName()) . ':' . urldecode($fileId);
    }
}
