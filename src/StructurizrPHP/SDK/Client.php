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

            $uri = $this->urlMap->workspaceURIPath($workspace->id());

            $this->get($workspace->id());
            die();
            $response = $this->httpClient->sendRequest(
                $this->httpRequestFactory->create(
                    $uri,
                    'PUT',
                    [
                        'X-Authorization' => $this->credentials->apiKey() . ':' . \base64_encode($this->credentials->hmac('PUT', $uri, $nonce, $workspaceDefinition)),
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

    public function get(string $workspaceId) : Workspace
    {
        $nonce = \time() * 1000;

        try {
//            $response = $this->httpClient->sendRequest(
//                $this->httpRequestFactory->create(
//                    $this->urlMap->workspaceUrl($workspaceId),
//                    'GET',
//                    [
//                        'X-Authorization' => $this->credentials->apiKey() . ':' . \base64_encode($this->credentials->hmac('GET', $this->urlMap->workspaceURIPath($workspaceId), $nonce, null)),
//                        'Nonce' => $nonce,
//                        'User-Agent' => self::AGENT_NAME,
//                        'Content-Type' => 'application/json; charset=UTF-8',
//                        'Content-MD5' => \base64_encode("d41d8cd98f00b204e9800998ecf8427e"),
//                    ],
//                    null
//                )
//            );

            /** @var array{id: string, name: string, description: string, model: array} $workspaceDefinition */

            $workspaceDefinition = (array) \json_decode(
                '{"id":48575,"name":"Shapes","description":"An example of all shapes available in Structurizr.","revision":35,"lastModifiedDate":"2019-11-03T13:50:42Z","lastModifiedUser":"norbert@orzechowicz.pl","lastModifiedAgent":"structurizr-web/1664","model":{"people":[{"id":"13","tags":"Element,Person, Person","name":"Person","description":"Description","location":"Unspecified"}],"softwareSystems":[{"id":"9","tags":"Element,Software System, Software System, Mobile Device Landscape","name":"Mobile Device Landscape","description":"Description","location":"Unspecified"},{"id":"5","tags":"Element,Software System, Software System, Hexagon","name":"Hexagon","description":"Description","location":"Unspecified"},{"id":"10","tags":"Element,Software System, Software System, Pipe","name":"Pipe","description":"Description","location":"Unspecified"},{"id":"7","tags":"Element,Software System, Software System, Web Browser","name":"WebBrowser","description":"Description","location":"Unspecified"},{"id":"6","tags":"Element,Software System, Software System, Cylinder","name":"Cylinder","description":"Description","location":"Unspecified"},{"id":"4","tags":"Element,Software System, Software System, Circle","name":"Circle","description":"Description","location":"Unspecified"},{"id":"11","tags":"Element,Software System, Software System, Folder","name":"Folder","description":"Description","location":"Unspecified"},{"id":"12","tags":"Element,Software System, Software System, Robot","name":"Robot","description":"Description","location":"Unspecified"},{"id":"1","tags":"Element,Software System, Software System, Box","name":"Box","description":"Description","location":"Unspecified"},{"id":"8","tags":"Element,Software System, Software System, Mobile Device Portrait","name":"Mobile Device Portrait","description":"Description","location":"Unspecified"},{"id":"3","tags":"Element,Software System, Software System, Ellipse","name":"Ellipse","description":"Description","location":"Unspecified"},{"id":"2","tags":"Element,Software System, Software System, RoundedBox","name":"RoundedBox","description":"Description","location":"Unspecified"}]},"documentation":{},"views":{"systemLandscapeViews":[{"description":"An example of all shapes available in Structurizr.","key":"shapes","paperSize":"A5_Landscape","enterpriseBoundaryVisible":true,"elements":[{"id":"11","x":30,"y":150},{"id":"12","x":1815,"y":55},{"id":"13","x":40,"y":975},{"id":"1","x":1590,"y":645},{"id":"2","x":550,"y":115},{"id":"3","x":485,"y":650},{"id":"4","x":2085,"y":1055},{"id":"5","x":15,"y":615},{"id":"6","x":1500,"y":1360},{"id":"7","x":1040,"y":580},{"id":"8","x":820,"y":945},{"id":"9","x":1185,"y":210},{"id":"10","x":1430,"y":980}]}],"configuration":{"branding":{},"styles":{"elements":[{"tag":"Element","width":650,"height":400,"background":"#438dd5","color":"#ffffff","fontSize":34,"border":"Solid","metadata":true},{"tag":"Box","width":450,"height":300,"shape":"Box","border":"Solid","metadata":true},{"tag":"RoundedBox","width":450,"height":300,"shape":"RoundedBox","border":"Solid","metadata":true},{"tag":"Ellipse","width":450,"height":300,"shape":"Ellipse","border":"Solid","metadata":true},{"tag":"Circle","width":450,"height":300,"shape":"Circle","border":"Solid","metadata":true},{"tag":"Cylinder","width":450,"height":300,"shape":"Cylinder","border":"Solid","metadata":true},{"tag":"Web Browser","width":450,"height":300,"shape":"WebBrowser","border":"Solid","metadata":true},{"tag":"Mobile Device Portrait","width":400,"height":650,"shape":"MobileDevicePortrait","border":"Solid","metadata":true},{"tag":"Mobile Device Landscape","width":450,"height":300,"shape":"MobileDeviceLandscape","border":"Solid","metadata":true},{"tag":"Pipe","width":450,"height":300,"shape":"Pipe","border":"Solid","metadata":true},{"tag":"Folder","width":450,"height":300,"shape":"Folder","border":"Solid","metadata":true},{"tag":"Hexagon","width":450,"height":300,"shape":"Hexagon","border":"Solid","metadata":true},{"tag":"Robot","width":550,"height":300,"shape":"Robot","border":"Solid","metadata":true},{"tag":"Person","width":550,"height":300,"shape":"Person","border":"Solid","metadata":true}]},"terminology":{},"lastSavedView":"shapes"}}}',
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
