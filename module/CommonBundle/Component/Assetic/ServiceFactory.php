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
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Assetic;

use Zend\ServiceManager\ServiceLocatorInterface as ServiceLocator,
    Zend\ServiceManager\ServiceManager,
    Zend\ServiceManager\Config;

class ServiceFactory extends \AsseticBundle\ServiceFactory
{
    public function createService(ServiceLocator $serviceLocator)
    {
        $asseticService = parent::createService($serviceLocator);
        $filterManager = $asseticService->getFilterManager();

        $configuration = (array) $serviceLocator->get('Config');
        $configuration = array_key_exists('assetic_filters', $configuration)
            ? $configuration['assetic_filters']
            : array();
        $configuration = new Config($configuration);

        $filterManager->setServiceLocator(new ServiceManager($configuration));

        return $asseticService;
    }
}
