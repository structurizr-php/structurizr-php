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

namespace StructurizrPHP\StructurizrPHP\SDK;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use StructurizrPHP\StructurizrPHP\Core\Workspace;
use StructurizrPHP\StructurizrPHP\Http\RequestFactory;
use StructurizrPHP\StructurizrPHP\SDK\Exception\Exception;

final class Client
{
    public const AGENT_NAME = 'structurizr-php/structurizr-php:0.0.1';

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

    /**
     * @var bool
     */
    private $mergeFromRemote;

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
        $this->mergeFromRemote = true;
    }

    /**
     * Specifies whether the layout of diagrams from a remote workspace should be retained when putting
     * a new version of the workspace.
     *
     * @param bool $mergeFromRemote   true if layout information should be merged from the remote workspace, false otherwise
     */
    public function setMergeRemote(bool $mergeFromRemote) : void
    {
        $this->mergeFromRemote = $mergeFromRemote;
    }

    public function put(Workspace $workspace) : void
    {
        if ($this->mergeFromRemote) {
            $remoteWorkspace = $this->get($workspace->id());
            if ($remoteWorkspace !== null) {
                $workspace->getViews()->copyLayoutInformationFrom($remoteWorkspace->getViews());
                $workspace->getViews()->getConfiguration()->copyConfigurationFrom($remoteWorkspace->getViews()->getConfiguration());
            }
        }

        try {
            $nonce = (int) \round(\microtime(true) * 1000);

            $workspaceDefinition = \json_encode($workspace->toArray(self::AGENT_NAME), JSON_THROW_ON_ERROR);

            $url = $this->urlMap->workspaceUrl($workspace->id());

            $response = $this->httpClient->sendRequest(
                $this->httpRequestFactory->create(
                    $url,
                    'PUT',
                    [
                        'X-Authorization' => $this->credentials->apiKey() . ':' . \base64_encode($this->credentials->hmac('PUT', $this->urlMap->workspaceURIPath($workspace->id()), $nonce, $workspaceDefinition)),
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

    public function get(string $workspaceId) : ?Workspace
    {
        $nonce = (int) \round(\microtime(true) * 1000);

        try {
            $response = $this->httpClient->sendRequest(
                $this->httpRequestFactory->create(
                    $this->urlMap->workspaceUrl($workspaceId),
                    'GET',
                    [
                        'X-Authorization' => $this->credentials->apiKey() . ':' . \base64_encode($this->credentials->hmac('GET', $this->urlMap->workspaceURIPath($workspaceId), $nonce, null)),
                        'Nonce' => $nonce,
                        'User-Agent' => self::AGENT_NAME,
                        'Content-Type' => 'application/json; charset=UTF-8',
                        'Content-MD5' => \base64_encode("d41d8cd98f00b204e9800998ecf8427e"),
                    ],
                    null
                )
            );


            if ($response->getStatusCode() === 401) {
                return null;
            }

            if ($response->getStatusCode() !== 200) {
                throw new Exception(\sprintf("Invalid API responses, expected 200, got %d", $response->getStatusCode()));
            }

            $workspaceDefinition = (array) \json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            return Workspace::hydrate($workspaceDefinition);
        } catch (ClientExceptionInterface $e) {
            throw new Exception('Can\'t put Workspace', 0, $e);
        }
    }
}
