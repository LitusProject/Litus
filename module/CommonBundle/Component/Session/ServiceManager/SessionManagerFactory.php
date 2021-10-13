<?php

namespace CommonBundle\Component\Session\ServiceManager;

use Interop\Container\ContainerInterface;
use Laminas\Session\SessionManager;
use Laminas\Session\Validator\RemoteAddr;

/**
 * Factory to instantiate the session container.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class SessionManagerFactory extends \Laminas\Session\Service\SessionManagerFactory
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return SessionManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');

        foreach ($config['session_manager']['validators'] as $validator) {
            switch ($validator) {
                case RemoteAddr::class:
                    if ($config['proxy']['use_proxy']) {
                        RemoteAddr::setUseProxy($config['proxy']['use_proxy']);
                        RemoteAddr::setTrustedProxies($config['proxy']['trusted_proxies']);

                        if (isset($config['proxy']['proxy_header'])) {
                            RemoteAddr::setProxyHeader($config['proxy']['proxy_header']);
                        }
                    }

                    break;
            }
        }

        return parent::__invoke($container, $requestedName, $options);
    }
}
