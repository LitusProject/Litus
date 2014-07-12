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
    Zend\Form\FieldsetInterface,
    Zend\Form\FormInterface,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputProviderInterface,
    Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

/**
 * Extending Zend's form component, so that our forms look the way we want
 * them to.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class Form extends \Zend\Form\Form implements InputFilterAwareInterface, ServiceLocatorAwareInterface, FieldsetInterface
{
    use \CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
    use \Zend\ServiceManager\ServiceLocatorAwareTrait;

    use ElementTrait;
    use FieldsetTrait;

    /**
     * @param null|string|int $name               Optional name for the element
     * @param array           $options
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setUseInputFilterDefaults(true)
            ->setAttribute('novalidate', true);
    }

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

    public function showAs()
    {
        return 'div';
    }

    /**
     * Adds a submit button to the form.
     *
     * @param  string $value
     * @param  string $class
     * @param  string $name
     * @return self
     */
    public function addSubmit($value, $class = null, $name = 'submit')
    {
        $submit = array(
            'type'       => 'submit',
            'name'       => $name,
            'value'      => $value,
        );

        if ($class)
            $submit['attributes'] = array(
                'class' => $class,
            );

        $this->add($submit);

        return $this;
    }

    /**
     * Adds a fieldset to the form.
     *
     * @param  string     $label
     * @param  string     $name
     * @return Collection
     */
    public function addFieldset($label, $name)
    {
        $fieldset = new Fieldset($name);
        $fieldset->setLabel($label);

        $this->getFormFactory()->configureFieldset($fieldset, array());

        $this->add($fieldset);

        return $fieldset;
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
        if (!$this->hasValidated())
            throw new RuntimeException('Please validate the form before extracting its data');
        if (!$this->isValid())
            throw new RuntimeException('Cannot hydrate object with data from an invalid form');

        return $this->getHydrator()->hydrate($this->getData(), $object);
    }

    /**
     * Validate the form
     *
     * Typically, will proxy to the composed input filter.
     *
     * @return bool
     * @throws Zend\Form\Exception\DomainException
     */
    public function isValid()
    {
        $this->injectSelfInValidators($this);

        return parent::isValid();
    }

    private function injectSelfInValidators(FieldsetInterface $fieldset)
    {
        foreach ($fieldset->getElements() as $element) {
            if (!$element instanceof InputProviderInterface)
                continue;
            $spec = $element->getInputSpecification();

            if (isset($spec['validators'])) {
                foreach ($spec['validators'] as $validator) {
                    if ($validator instanceof FormAwareInterface)
                        $validator->setForm($this);
                }
            }
        }

        foreach ($fieldset->getFieldsets() as $child) {
            $this->injectSelfInValidators($child);
        }
    }
}
