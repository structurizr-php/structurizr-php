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

namespace StructurizrPHP\Core\Model;

use StructurizrPHP\Core\Assertion;

final class HttpHealthCheck
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $url;

    /**
     * The polling interval, in seconds.
     *
     * @var int
     */
    private $interval;

    /**
     * The timeout after which a health check is deemed as failed, in milliseconds.
     *
     * @var int
     */
    private $timeout;

    /**
     * The headers that should be sent in the HTTP request.
     *
     * @var array<string, string>
     */
    private $headers;

    public function __construct(string $name, string $url, int $interval, int $timeout)
    {
        $this->name = $name;
        $this->url = $url;
        $this->interval = $interval;
        $this->timeout = $timeout;
        $this->headers = [];
    }

    public static function hydrate(array $healthCheckData) : self
    {
        $healthCheck = new self(
            $healthCheckData['name'],
            $healthCheckData['url'],
            (int) $healthCheckData['interval'],
            (int) $healthCheckData['timeout'],
        );

        if (isset($healthCheckData['headers'])) {
            $healthCheck->setHeaders($healthCheckData['headers']);
        }

        return $healthCheck;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function setName(string $name) : void
    {
        Assertion::notEmpty($name);
        $this->name = $name;
    }

    public function url() : string
    {
        return $this->url;
    }

    public function setUrl(string $url) : void
    {
        Assertion::url($url);

        $this->url = $url;
    }

    public function getInterval() : int
    {
        return $this->interval;
    }

    public function setInterval(int $interval) : void
    {
        Assertion::greaterThan($interval, 0);

        $this->interval = $interval;
    }

    public function timeout() : int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout) : void
    {
        Assertion::greaterThan($timeout, 0);

        $this->timeout = $timeout;
    }

    /**
     * @return array<string,string>
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * @param array<string,string> $headers
     */
    public function setHeaders(array $headers) : void
    {
        $this->headers = $headers;
    }

    public function toArray() : array
    {
        $data = [
            'name' => $this->name,
            'url' => $this->url,
            'interval' => $this->interval,
            'timeout' => $this->timeout,
        ];

        if (\count($this->headers)) {
            $data['headers'] = $this->headers;
        }

        return $data;
    }
}
