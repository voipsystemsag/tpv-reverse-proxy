<?php

require_once('../vendor/autoload.php');

// Load ENV vars
$env = new \Symfony\Component\Dotenv\Dotenv();
$env->load(__DIR__ . '/../.env');

// For educational purposes. You SHOULD save it encoded-ready in a secure place.
$authHash = password_hash($_ENV['PROXY_AUTH'], PASSWORD_ARGON2I);

if (!isset($_REQUEST['auth']) || !password_verify($_REQUEST['auth'], $authHash)) {
    header('HTTP/1.1 401 Unauthorized');
    exit('Unauthorized');
}

// Proxy definition and request handling.
$proxy = (new \Tpv\ReverseProxy\RestProxy($_ENV['TPV_TOKEN']))
    ->setBaseUrl($_ENV['BASE_URL'])
    ->setForbiddenEndpoints([
        // Deny access to "/accounts" endpoint due it exposes main account details.
        '^accounts',
    ])
;

try {
    $proxy->handleRequest();
} catch (\Tpv\ReverseProxy\ForbiddenEndpointException $ex) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied to this resource');
}
exit;
