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

namespace StructurizrPHP\StructurizrPHP\Infrastructure\Serializer;

use StructurizrPHP\StructurizrPHP\SDK\JsonSerializer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

final class SymfonyJsonSerializer implements JsonSerializer
{
    /**
     * @var SymfonySerializer
     */
    private $symfonySerializer;

    public function __construct(SymfonySerializer $symfonySerializer)
    {
        $this->symfonySerializer = $symfonySerializer;
    }

    public static function create() : self
    {
        return new self(
            new SymfonySerializer()
        );
    }

    public function serialize(object $object): string
    {
        return $this->symfonySerializer->serialize($object, 'json');
    }
}
