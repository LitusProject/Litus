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

namespace CommonBundle\Component\Session\ServiceManager;

use Interop\Container\ContainerInterface;
use Zend\Session\Validator\RemoteAddr;

/**
 * Factory to instantiate the session container.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class SessionManagerFactory extends \Zend\Session\Service\SessionManagerFactory
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Container
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
