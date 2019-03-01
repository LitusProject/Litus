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

namespace CommonBundle\Component\Form;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\ClassMethods as ClassMethodsHydrator;

/**
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class FormElementManager extends \Zend\Form\FormElementManager
{
    /**
     * @var ClassMethodsHydrator
     */
    private $hydrator;

    /**
     * @var array|null Associative array of data to inject into the form
     */
    private $data;

    /**
     * @param \Zend\ServiceManager\ConfigInterface|ContainerInterface|null $configInstanceOrParentLocator
     * @param array                                                        $config
     */
    public function __construct($configInstanceOrParentLocator = null, array $config = array())
    {
        // Add initializer before the parent constructor, because we want this
        // to be the bottom of the stack before parent::__construct is called.
        $this->addInitializer(array($this, 'injectServiceLocator'));

        parent::__construct($configInstanceOrParentLocator, $config);
        $this->addInitializer(array($this, 'hydrate'));

        $this->hydrator = new ClassMethodsHydrator();
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
}
