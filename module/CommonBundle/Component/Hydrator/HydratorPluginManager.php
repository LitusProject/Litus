<?php

namespace CommonBundle\Component\Hydrator;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use Interop\Container\ContainerInterface;
use RuntimeException;

/**
 * Manager for our hydrators
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class HydratorPluginManager extends \Laminas\Hydrator\HydratorPluginManager
{
    /**
     * @param \Laminas\ServiceManager\ConfigInterface|ContainerInterface|null $configInstanceOrParentLocator
     * @param array                                                           $config
     */
    public function __construct($configInstanceOrParentLocator = null, array $config = array())
    {
        // Add initializer before the parent constructor, because we want this
        // to be the bottom of the stack before parent::__construct is called.
        $this->addInitializer(array($this, 'injectServiceLocator'));

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
     * @param  string $name
     * @param  array  $options
     * @return object|array
     */
    public function get($name, $options = array())
    {
        if (!$this->has($name)) {
            if (strpos($name, '\\') === 0) {
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

        if ($parts[1] !== 'Entity') {
            return $name;
        }
        $parts[1] = 'Hydrator';

        return implode('\\', $parts);
    }
}
