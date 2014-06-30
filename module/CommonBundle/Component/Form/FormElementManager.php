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

use RuntimeException,
    Zend\Form\FormFactoryAwareInterface,
    Zend\ServiceManager\ConfigInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    CommonBundle\Component\Util\String as StringUtil,
    Zend\Stdlib\Hydrator\ClassMethods as ClassMethodHydrator;

/**
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class FormElementManager extends \Zend\Form\FormElementManager
{
    /**
     * @var string the pattern to match
     */
    const PATTERN = '/^([^_]+)(_admin)?_(([^_]+_)+[^_]+)$/';

    /**
     * @var boolean Whether this is an admin form element manager
     */
    private $isAdmin;

    /**
     * @var ServiceLocatorInterface The main service locator
     */
    private $mainServiceLocator;

    /**
     * @var ClassMethodHydrator Hydrator to inject data into the form
     */
    private $hydrator;

    /**
     * @var array|null Associative array of data to inject into the form
     */
    private $data;

    /**
     * @param ConfigInterface         $configuration
     * @param boolean                 $isAdmin
     * @param ServiceLocatorInterface $mainServiceLocator
     */
    public function __construct(ConfigInterface $config, $isAdmin, ServiceLocatorInterface $mainServiceLocator)
    {
        parent::__construct($config);

        $this->isAdmin = (bool) $isAdmin;
        $this->mainServiceLocator = $mainServiceLocator;
        $this->hydrator = new ClassMethodHydrator();
    }

    /**
     * Inject the factory to any element that implements FormFactoryAwareInterface
     *
     * @param $element
     */
    public function injectFactory($element)
    {
        if ($element instanceof FormFactoryAwareInterface) {
            $factory = new Factory($this);
            $element->setFormFactory($factory);

            if ($this->serviceLocator instanceof ServiceLocatorInterface
                && $this->serviceLocator->has('InputFilterManager')
            ) {
                $inputFilters = $this->serviceLocator->get('InputFilterManager');
                $factory->getInputFilterFactory()->setInputFilterManager($inputFilters);
            }
        }
    }

    public function validatePlugin($plugin)
    {
        // inject main service locator instance
        // we need to do this manually because this FormElementManager is the
        // defualt ServiceLocator that gets injected, which is of course wrong
        if ($plugin instanceof ServiceLocatorAwareInterface)
            $plugin->setServiceLocator($this->mainServiceLocator);

        if (null !== $this->data)
            $this->hydrator->hydrate($this->data, $plugin);
        $this->data = null;

        return parent::validatePlugin($plugin);
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
     * @param  bool         $usePeeringServiceManagers
     * @return object
     */
    public function get($name, $options = array(), $usePeeringServiceManagers = true)
    {
        if ($this->has($name)) {
            return parent::get($name, $options, $usePeeringServiceManagers);
        }

        $matches = array();

        if (!preg_match(self::PATTERN, $name, $matches)) {
            throw new RuntimeException('Unknown form element: ' . $name);
        }

        if (!$this->isAdmin && '' != $matches[2]) {
            throw new RuntimeException('Cannot create admin form through non-admin FormElementManager');
        }

        $bundle = StringUtil::underscoredToCamelCase($matches[1]) . 'Bundle\Form\\';
        $type = $this->isAdmin ? 'Admin\\' : '';
        $form = implode('\\', array_map('CommonBundle\Component\Util\String::underscoredToCamelCase', explode('_', $matches[3])));

        $this->setInvokableClass($name, $bundle . $type . $form);

        return parent::get($name, $options, $usePeeringServiceManagers);
    }
}
