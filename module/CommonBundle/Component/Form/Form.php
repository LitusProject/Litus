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

use CommonBundle\Component\Form\Element\Csrf;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\DoctrineTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\HydratorPluginManagerTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\SessionContainerTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Component\Validator\FormAwareInterface;
use RuntimeException;
use Zend\Form\FieldsetInterface as ZendFieldsetInterface;
use Zend\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputInterface;

/**
 * Extending Zend's form component, so that our forms look the way we want
 * them to.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class Form extends \Zend\Form\Form implements InputFilterAwareInterface, ServiceLocatorAwareInterface, ZendFieldsetInterface
{
    use FieldsetTrait;

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

        $this->setUseInputFilterDefaults(true);
        $this->add(
            new Csrf('csrf')
        );
    }

    /**
     * @return \Zend\Hydrator\HydratorInterface
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
                'options' => array(
                    'label' => $label,
                ),
            )
        );

        return $this->get($name);
    }

    /**
     * Adds a submit button to the form.
     *
     * @param  string      $value
     * @param  string|null $icon
     * @param  string      $name
     * @param  array       $attributes
     * @return self
     */
    public function addSubmit($value, $icon = null, $name = 'submit', $attributes = array())
    {
        $options = array();
        if ($icon !== null) {
            $options = array(
                'font_awesome' => 'fa-' . $icon,
            );
        }

        $this->add(
            array(
                'type'       => 'submit',
                'name'       => $name,
                'label'      => $value,
                'value'      => $value,
                'attributes' => $attributes,
                'options'    => $options,
            )
        );

        return $this;
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
     * @return \Zend\InputFilter\Factory
     */
    public function getInputFilterFactory()
    {
        return $this->getFormFactory()->getInputFilterFactory();
    }

    /**
     * Hydrates the given object with the data from this form.
     * If the object is null, a new object will be created by the hydrator.
     *
     * This method does nothing if the form is invalid.
     *
     * @param  object|null $object The object to hydrate
     * @return object           The hydrated object
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
     * @return \CommonBundle\Entity\General\Language
     */
    public function getLanguage()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev($this->getSessionContainer()->language);
    }
}
