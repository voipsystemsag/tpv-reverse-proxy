<?php


namespace Tpv\ReverseProxy;

use Throwable;

class ForbiddenEndpointException extends \RuntimeException
{
    public function __construct(string $endpoint, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Access denied to the resource "%s"', $endpoint), $code, $previous);
    }
}
