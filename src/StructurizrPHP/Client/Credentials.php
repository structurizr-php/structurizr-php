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

namespace StructurizrPHP\Client;

final class Credentials
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiSecret;

    public function __construct(string $apiKey, string $apiSecret)
    {
        Assertion::notEmpty($apiKey);
        Assertion::notEmpty($apiSecret);

        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function apiKey() : string
    {
        return $this->apiKey;
    }

    public function hmac(string $werb, string $uri, int $nonce, ?string $workspaceDefinition) : string
    {
        $messageDigest = \sprintf(
            "%s\n%s\n%s\n%s\n%d\n",
            $werb,
            $uri,
            (!$workspaceDefinition) ? 'd41d8cd98f00b204e9800998ecf8427e' : \md5($workspaceDefinition),
            ($werb === 'PUT') ? 'application/json; charset=UTF-8' : '',
            $nonce
        );

        return \hash_hmac('sha256', $messageDigest, $this->apiSecret);
    }
}
