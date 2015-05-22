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

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface,
    CommonBundle\Component\Validator\FormAwareInterface,
    RuntimeException,
    Zend\Form\FieldsetInterface as ZendFieldsetInterface,
    Zend\Form\FormInterface,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Zend\InputFilter\InputInterface,
    Zend\InputFilter\InputProviderInterface,
    Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

/**
 * Extending Zend's form component, so that our forms look the way we want
 * them to.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class Form extends \Zend\Form\Form implements InputFilterAwareInterface, ServiceLocatorAwareInterface, ZendFieldsetInterface
{
    use \CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
    use \Zend\ServiceManager\ServiceLocatorAwareTrait;

    use ElementTrait, FieldsetTrait {
        FieldsetTrait::setRequired insteadof ElementTrait;
        ElementTrait::setRequired as setElementRequired;
    }

    /**
     * @param null|string|int $name    Optional name for the element
     * @param array           $options
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setUseInputFilterDefaults(true)
            ->setAttribute('novalidate', true);
    }

    /**
     * @return \Zend\Stdlib\Hydrator\HydratorInterface
     */
    public function getHydrator()
    {
        if (null === $this->hydrator) {
            $this->setHydrator(new ClassMethodsHydrator());
        } elseif (is_string($this->hydrator)) {
            $this->setHydrator(
                $this->getServiceLocator()
                    ->get('litus.hydratormanager')
                    ->get($this->hydrator)
            );
        }

        return parent::getHydrator();
    }

    /**
     * @return \CommonBundle\Entity\General\Language
     */
    public function getLanguage()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev($this->getSessionStorage()->language);
    }

    /**
     * @return string
     */
    public function showAs()
    {
        return 'div';
    }

    /**
     * Adds a submit button to the form.
     *
     * @param  string      $value
     * @param  string|null $class
     * @param  string      $name
     * @param  array       $attributes
     * @return self
     */
    public function addSubmit($value, $class = null, $name = 'submit', $attributes = array())
    {
        $submit = array(
            'type'       => 'submit',
            'name'       => $name,
            'value'      => $value,
        );

        if ($class) {
            $attributes['class'] = $class;
        }

        $submit['attributes'] = $attributes;

        $this->add($submit);

        return $this;
    }

    /**
     * Adds a fieldset to the form.
     *
     * @param  string   $label
     * @param  string   $name
     * @return Fieldset
     */
    public function addFieldset($label, $name)
    {
        $this->add(array(
            'type' => 'fieldset',
            'name' => $name,
            'label' => $label,
        ));

        return $this->get($name);
    }

    /**
     * @return \Zend\InputFilter\Factory
     */
    public function getInputFilterFactory()
    {
        return $this->getFormFactory()->getInputFilterFactory();
    }

    /**
     * {@inheritdoc} Defaults to VALUES_AS_ARRAY.
     */
    public function getData($flags = FormInterface::VALUES_AS_ARRAY)
    {
        return parent::getData($flags);
    }

    /**
     * Hydrates the given object with the data from this form.
     * If the object is null, a new object will be created by the hydrator.
     *
     * This method does nothing if the form is invalid.
     *
     * @param  object|null      $object The object to hydrate
     * @return object           The hydrated object
     * @throws RuntimeException If $object is null and creating objects is not supported by the hydrator
     * @throws RuntimeException If this form hasn't been validated yet
     * @throws RuntimeException If this form is invalid
     */
    public function hydrateObject($object = null)
    {
        if (!$this->hasValidated()) {
            throw new RuntimeException('Please validate the form before extracting its data');
        }
        if (!$this->isValid()) {
            throw new RuntimeException('Cannot hydrate object with data from an invalid form');
        }

        return $this->getHydrator()->hydrate($this->getData(), $object);
    }

    private function injectSelfInValidators(InputFilterInterface $filter)
    {
        foreach ($filter->getInputs() as $key => $input) {
            if ($input instanceof InputInterface) {
                foreach ($input->getValidatorChain()->getValidators() as $validator) {
                    if ($validator['instance'] instanceof FormAwareInterface) {
                        $validator['instance']->setForm($this);
                    }
                }
            } else {
                $this->injectSelfInValidators($input);
            }
        }
    }

    public function getInputFilter()
    {
        if (!isset($this->filter)) {
            $specification = $this->getInputFilterSpecification();

            $this->filter = $this->getInputFilterFactory()
                ->createInputFilter($specification);

            if ($this->filter instanceof InputFilterInterface) {
                $this->injectSelfInValidators($this->filter);
            }
        }

        return $this->filter;
    }
}
