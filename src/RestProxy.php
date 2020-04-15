<?php


namespace Tpv\ReverseProxy;

use Sabre\HTTP\Client;
use Sabre\HTTP\Sapi;

class RestProxy
{
    const TPV_ENDPOINT = 'https://www.iplogin.net/tpv/rest/';

    /**
     * @var string
     */
    private $version;

    /**
     * @var null|string
     */
    private $token;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var array
     */
    private $forbiddenEndpoints;

    public function __construct(?string $token = null, string $version = 'v2')
    {
        $this->token = $token;
        $this->version = $version;
        $this->baseUrl = '/';
        $this->forbiddenEndpoints = [];
    }

    /**
     * Handle the original request and send it to the TPV API.
     *
     * @throws \Sabre\HTTP\ClientException
     * @throws \Sabre\HTTP\ClientHttpException
     */
    public function handleRequest(): void
    {
        // Get the full HTTP request
        $request = Sapi::getRequest();

        $request->setBaseUrl($this->baseUrl);

        // Manipulate the request to be complaint with the proxy
        $subRequest = clone $request;

        // Check if the requested endpoint is forbidden
        $this->denyAccessIfForbiddenEndpoint($subRequest->getPath());

        $subRequest->removeHeader('Host');
        $subRequest->addHeader('X-API-KEY', $this->token);
        $subRequest->setUrl(
            self::TPV_ENDPOINT
            . "{$this->version}/"
            . $subRequest->getPath()
            . '?'
            . http_build_query($subRequest->getQueryParameters())
        );

        // Send the proxyfied request
        $client = new Client();
        $response = $client->send($subRequest);

        // Remove transfer-encoding header due compatiblity issues.
        $response->removeHeader('Transfer-Encoding');

        Sapi::sendResponse($response);
    }

    /**
     * Throws a {@see ForbiddenEndpointException} if the endpoint matches any of
     * the rules in {@see RestProxy::$forbiddenEndpoints}}.
     * @param string $endpoint
     *
     * @throws ForbiddenEndpointException Thrown when the endpoint matches any
     * of the forbidden rules.
     */
    public function denyAccessIfForbiddenEndpoint(string $endpoint): void
    {
        /** @var string $rule */
        foreach ($this->forbiddenEndpoints as $rule) {
            if (preg_match('/' . $rule . '/', $endpoint)) {
                throw new ForbiddenEndpointException($endpoint);
            }
        }
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return RestProxy
     */
    public function setVersion(string $version): RestProxy
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return RestProxy
     */
    public function setToken(?string $token): RestProxy
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     * @return RestProxy
     */
    public function setBaseUrl(string $baseUrl): RestProxy
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * @return array
     */
    public function getForbiddenEndpoints(): array
    {
        return $this->forbiddenEndpoints;
    }

    /**
     * @param array $forbiddenEndpoints
     * @return RestProxy
     */
    public function setForbiddenEndpoints(array $forbiddenEndpoints): RestProxy
    {
        $this->forbiddenEndpoints = $forbiddenEndpoints;

        return $this;
    }
}
