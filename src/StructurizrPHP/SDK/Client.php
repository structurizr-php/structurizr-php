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

namespace StructurizrPHP\StructurizrPHP\SDK;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use StructurizrPHP\StructurizrPHP\Core\Workspace;
use StructurizrPHP\StructurizrPHP\Http\RequestFactory;
use StructurizrPHP\StructurizrPHP\SDK\Exception\Exception;

final class Client
{
    public const AGENT_NAME = 'structurizr-php/sdk:0.0.1';

    /**
     * @var Credentials
     */
    private $credentials;

    /**
     * @var UrlMap
     */
    private $urlMap;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactory
     */
    private $httpRequestFactory;

    public function __construct(
        Credentials $credentials,
        UrlMap $urlMap,
        ClientInterface $httpClient,
        RequestFactory $httpRequestFactory
    ) {
        $this->urlMap = $urlMap;
        $this->httpClient = $httpClient;
        $this->httpRequestFactory = $httpRequestFactory;
        $this->credentials = $credentials;
    }

    public function put(Workspace $workspace) : void
    {
        $nonce = \time() * 1000;

        try {
            $workspaceDefinition = \json_encode($workspace->toArray(self::AGENT_NAME), JSON_THROW_ON_ERROR);
            $response = $this->httpClient->sendRequest(
                $this->httpRequestFactory->create(
                    $this->urlMap->workspaceUrl($workspace),
                    'PUT',
                    [
                        'X-Authorization' => $this->credentials->apiKey() . ':' . \base64_encode($this->credentials->hmac('PUT', $this->urlMap->workspaceURIPath($workspace), $nonce, $workspaceDefinition)),
                        'Nonce' => $nonce,
                        'User-Agent' => self::AGENT_NAME,
                        'Content-Type' => 'application/json; charset=UTF-8',
                        'Content-MD5' => \base64_encode(\md5($workspaceDefinition)),
                    ],
                    $workspaceDefinition
                )
            );

            if ($response->getStatusCode() !== 200) {
                throw new Exception(\sprintf('Status: %d, Message: %s', $response->getStatusCode(), $response->getBody()->getContents()));
            }
        } catch (ClientExceptionInterface $e) {
            throw new Exception('Can\'t put Workspace', 0, $e);
        }
    }
}
