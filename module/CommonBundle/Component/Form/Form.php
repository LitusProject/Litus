<?php

namespace CommonBundle\Component\Form;

use CommonBundle\Component\ServiceManager\ServiceLocatorAware\DoctrineTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\HydratorPluginManagerTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\SessionContainerTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Component\Validator\FormAwareInterface;
use CommonBundle\Entity\General\Organization\Unit;
use Laminas\Form\FieldsetInterface as LaminasFieldsetInterface;
use Laminas\Form\FormInterface;
use Laminas\Hydrator\ClassMethods as ClassMethodsHydrator;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\InputInterface;
use RuntimeException;

/**
 * Extending Laminas's form component, so that our forms look the way we want
 * them to.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class Form extends \Laminas\Form\Form implements InputFilterAwareInterface, ServiceLocatorAwareInterface, LaminasFieldsetInterface
{
    use ElementTrait {
        ElementTrait::setRequired as setElementRequired;
    }

    use FieldsetTrait {
        FieldsetTrait::setRequired insteadof ElementTrait;
    }

    use ServiceLocatorAwareTrait;

    use DoctrineTrait;
    use HydratorPluginManagerTrait;
    use SessionContainerTrait;

    /**
     * @param string|integer|null $name    Optional name for the element
     * @param array               $options
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setUseInputFilterDefaults(true)
            ->setAttribute('novalidate', true);
    }

    /**
     * @return \Laminas\Hydrator\HydratorInterface
     */
    public function getHydrator()
    {
        if ($this->hydrator === null) {
            $this->setHydrator(new ClassMethodsHydrator());
        } elseif (is_string($this->hydrator)) {
            $this->setHydrator(
                $this->getHydratorPluginManager()->get($this->hydrator)
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
            ->findOneByAbbrev($this->getSessionContainer()->language);
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
            'type'  => 'submit',
            'name'  => $name,
            'value' => $value,
        );

        if ($class !== null) {
            $attributes['class'] = $class;
        }

        $submit['attributes'] = $attributes;

        $this->add($submit);

        return $this;
    }

    /**
     * Adds a fieldset to the form.
     *
     * @param  string $label
     * @param  string $name
     * @return Fieldset
     */
    public function addFieldset($label, $name)
    {
        $this->add(
            array(
                'type'  => 'fieldset',
                'name'  => $name,
                'label' => $label,
            )
        );

        return $this->get($name);
    }

    /**
     * @return \Laminas\InputFilter\Factory
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
     * @param  object|null $object The object to hydrate
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
        foreach ($filter->getInputs() as $input) {
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

    /**
     * Get the current academic year.
     *
     * @param  boolean $organization
     * @return AcademicYear
     */
    protected function getCurrentAcademicYear($organization = false)
    {
        if ($organization) {
            return AcademicYear::getOrganizationYear($this->getEntityManager());
        }

        return AcademicYear::getUniversityYear($this->getEntityManager());
    }

    /**
     * @param $academic
     * @return array
     */
    protected function createUnitsArray($academic): array
    {
        // TODO: Check if this works
        $units = array();

        if ($academic->isPraesidium($this->getCurrentAcademicYear(true))
            && $academic->isInWorkingGroup($this->getCurrentAcademicYear(true))
        ) {
            $units = array_merge(
                $this->getEntityManager()
                    ->getRepository(Unit::class)
                    ->findAllActiveAndDisplayed()->getResult(),
                $this->getEntityManager()
                    ->getRepository(Unit::class)
                    ->findAllActiveAndDisplayedAndWorkgroupQuery()->getResult(),
            );
        }

        if ($academic->isPraesidium($this->getCurrentAcademicYear(true))) {
            $units = $this->getEntityManager()
                ->getRepository(Unit::class)
                ->findAllActiveAndDisplayedQuery()->getResult();
        }

        if ($academic->isInWorkingGroup($this->getCurrentAcademicYear(true))) {
            $units = $this->getEntityManager()
                ->getRepository(Unit::class)
                ->findAllActiveAndDisplayedAndWorkgroupQuery()->getResult();
        }

        $unitsArray = array();
        foreach ($units as $unit) {
            $unitsArray[$unit->getId()] = $unit->getName();
        }

        return $unitsArray;
    }
}
