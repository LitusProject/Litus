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

namespace CommonBundle\Component\Hydrator;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use Interop\Container\ContainerInterface;
use RuntimeException;
use Zend\Hydrator\HydratorInterface;

/**
 * Manager for our hydrators
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class HydratorPluginManager extends \Zend\Hydrator\HydratorPluginManager
{
    /**
     * @param null|ConfigInterface|ContainerInterface $configInstanceOrParentLocator
     * @param array                                   $config
     */
    public function __construct($configInstanceOrParentLocator = null, array $config = [])
    {
        // Add initializer before the parent constructor, because we want this
        // to be the bottom of the stack before parent::__construct is called.
        $this->addInitializer(array($this, 'injectServiceLocator'), false);

        parent::__construct($configInstanceOrParentLocator, $config);
    }

    /**
     * Inject the service locator into any element implementing
     * ServiceLocatorAwareInterface.
     *
     * @param  ContainerInterface $container
     * @param  mixed              $instance
     * @return void
     */
    public function injectServiceLocator(ContainerInterface $container, $instance)
    {
        if (!$instance instanceof ServiceLocatorAwareInterface) {
            return;
        }

        $instance->setServiceLocator($container);
    }

    /**
     * @param  string       $name
     * @param  array        $options
     * @return object|array
     */
    public function get($name, $options = array())
    {
        if (!$this->has($name)) {
            if (0 === strpos($name, '\\')) {
                $name = substr($name, 1);
            }

            $hydratorName = '\\' . $this->getHydratorName($name);
            if (!class_exists($hydratorName)) {
                throw new RuntimeException('Unknown hydrator: ' . $hydratorName);
            }

            $this->setInvokableClass($name, $hydratorName);
        }

        return parent::get($name, $options);
    }

    /**
     * @param  string $name
     * @return string
     */
    private function getHydratorName($name)
    {
        $parts = explode('\\', $name, 3);

        if ('Entity' !== $parts[1]) {
            return $name;
        }
        $parts[1] = 'Hydrator';

        return implode('\\', $parts);
    }
}
