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

namespace CommonBundle\Component\Form;

use Zend\Filter\FilterChain,
    Zend\ServiceManager\Config,
    Zend\ServiceManager\ServiceLocatorInterface;

/**
 * A factory class for form factories.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class FactoryFactory implements \Zend\ServiceManager\FactoryInterface
{
    /**
     * @var bool
     */
    private $isAdmin;

    /**
     * @param bool
     */
    public function __construct($isAdmin)
    {
        $this->isAdmin = (bool) $isAdmin;
    }

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $config = $serviceManager->get('Config');
        $config = $config['litus']['forms'][$this->isAdmin ? 'admin' : 'bootstrap'];
        $config = new Config($config);

        $factory = new Factory(
            new FormElementManager($config, $this->isAdmin, $serviceManager)
        );

        $filterChain = new FilterChain();
        $filterChain->setPluginManager($serviceManager->get('FilterManager'));

        $factory->getInputFilterFactory()
            ->setDefaultFilterChain($filterChain);

        return $factory;
    }
}
