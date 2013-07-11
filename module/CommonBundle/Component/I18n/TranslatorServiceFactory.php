<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\I18n;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Translator
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TranslatorServiceFactory extends \Zend\I18n\Translator\TranslatorServiceFactory
{
    /**
     * Create the translation service.
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator The service locator
     * @return \CommonBundle\Component\I18n\Translator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('configuration');

        return Translator::factory(
            isset($config['translator']) ? $config['translator'] : array()
        );
    }
}
