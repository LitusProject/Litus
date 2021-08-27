<?php

if (file_exists(__DIR__ . '/../proxy.config.php')) {
    // TODO: Remove this branch once all deployments have been containerized
    $proxyConfig = include __DIR__ . '/../proxy.config.php';
} else {
    $trustedProxies = array();
    if (isset($_ENV['LITUS_PROXY_TRUSTED_PROXIES'])) {
        $trustedProxies = explode(', ', $_ENV['LITUS_PROXY_TRUSTED_PROXIES']);
    }

    $proxyConfig = array(
        'use_proxy'       => $_ENV['LITUS_PROXY_USE_PROXY'] ?? false,
        'trusted_proxies' => $trustedProxies,
    );
}

if ($proxyConfig['use_proxy'] && count($proxyConfig['trusted_proxies']) == 0) {
    throw new RuntimeException(
        'The proxy configuration did not specify any trusted proxies'
    );
}

return array(
    'proxy' => $proxyConfig,
);
