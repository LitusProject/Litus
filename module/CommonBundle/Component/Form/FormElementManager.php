<?php

namespace CommonBundle\Component\Form;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\Util\StringUtil;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormFactoryAwareInterface;
use Laminas\Hydrator\ClassMethods as ClassMethodsHydrator;
use RuntimeException;

/**
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class FormElementManager extends \Laminas\Form\FormElementManager
{
    /**
     * @var string
     */
    const NAME_REGEX = '/^([^_]+)(_admin)?_(([^_]+_)+[^_]+)$/';

    /**
     * @var boolean Whether this is an admin form element manager
     */
    private $isAdmin;

    /**
     * @var ClassMethodsHydrator
     */
    private $hydrator;

    /**
     * @var array|null Associative array of data to inject into the form
     */
    private $data;

    /**
     * @param boolean                                                         $isAdmin
     * @param \Laminas\ServiceManager\ConfigInterface|ContainerInterface|null $configInstanceOrParentLocator
     * @param array                                                           $config
     */
    public function __construct($isAdmin, $configInstanceOrParentLocator = null, array $config = array())
    {
        // Add initializer before the parent constructor, because we want this
        // to be the bottom of the stack before parent::__construct is called.
        $this->addInitializer(array($this, 'injectServiceLocator'));

        $this->isAdmin = $isAdmin;
        $this->hydrator = new ClassMethodsHydrator();

        parent::__construct($configInstanceOrParentLocator, $config);
        $this->addInitializer(array($this, 'hydrate'));
    }

    /**
     * Inject the factory into any element implementing
     * FormFactoryAwareInterface.
     *
     * @param  ContainerInterface $container
     * @param  mixed              $instance
     * @return void
     */
    public function injectFactory(ContainerInterface $container, $instance)
    {
        if (!$instance instanceof FormFactoryAwareInterface) {
            return;
        }

        $instance->setFormFactory(new Factory($this));

        if ($container->has('InputFilterManager')) {
            $instance->getFormFactory()
                ->getInputFilterFactory()
                ->setInputFilterManager($container->get('InputFilterManager'));
        }
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
     * Hydrate the element with the given data, if any.
     *
     * @param  ContainerInterface $container
     * @param  mixed              $instance
     * @return void
     */
    public function hydrate(ContainerInterface $container, $instance)
    {
        if ($this->data !== null) {
            $this->hydrator->hydrate($this->data, $instance);
        }

        $this->data = null;
    }

    /**
     * @param  array|null $data
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param  string       $name
     * @param  string|array $options
     * @param  boolean      $usePeeringServiceManagers
     * @return object|array
     */
    public function get($name, $options = array(), $usePeeringServiceManagers = true)
    {
        if (!$this->has($name)) {
            $matches = array();
            if (!preg_match(self::NAME_REGEX, $name, $matches)) {
                throw new RuntimeException('Unknown form element: ' . $name);
            }

            if (!$this->isAdmin && $matches[2] != '') {
                throw new RuntimeException('Cannot create admin form element through non-admin FormElementManager');
            }

            $bundle = StringUtil::underscoredToCamelCase($matches[1]) . 'Bundle\Form\\';
            $type = $this->isAdmin ? 'Admin\\' : '';
            $form = implode('\\', array_map('CommonBundle\Component\Util\StringUtil::underscoredToCamelCase', explode('_', $matches[3])));

            $this->setInvokableClass($name, $bundle . $type . $form);
        }

        return parent::get($name, $options, $usePeeringServiceManagers);
    }
}
