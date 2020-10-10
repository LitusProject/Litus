<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

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
