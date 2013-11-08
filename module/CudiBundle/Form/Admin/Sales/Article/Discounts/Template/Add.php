<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\Sales\Article\Discounts\Template;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\Price as PriceValidator,
    CudiBundle\Entity\Sale\Article\Discount\Discount,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Template
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Dario  Incalza <dario.incalza@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('name');
        $field->setAttribute('id', 'name')
            ->setLabel('Name')
            ->setRequired();
        $this->add($field);

        $field = new Text('value');
        $field->setAttribute('id', 'value')
            ->setLabel('Value')
            ->setRequired();
        $this->add($field);

        $field = new Select('method');
        $field->setAttribute('id', 'method')
            ->setLabel('Method')
            ->setAttribute('options', Discount::$POSSIBLE_METHODS)
            ->setRequired();
        $this->add($field);

        $field = new Select('type');
        $field->setAttribute('id', 'type')
            ->setLabel('Type')
            ->setRequired()
            ->setAttribute('options', Discount::$POSSIBLE_TYPES);
        $this->add($field);

        $field = new Select('organization');
        $field->setAttribute('id', 'organization')
            ->setAttribute('options', $this->_getOrganizations())
            ->setLabel('Organization')
            ->setRequired();
        $this->add($field);

        $field = new Select('rounding');
        $field->setAttribute('id', 'rounding')
            ->setLabel('Rounding')
            ->setRequired()
            ->setAttribute('options', $this->_getRoundings());
        $this->add($field);

        $field = new Checkbox('apply_once');
        $field->setAttribute('id', 'apply_once')
            ->setLabel('Apply Once');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'add');
        $this->add($field);
    }

    private function _getRoundings()
    {
        $roundings = array();
        foreach(Discount::$POSSIBLE_ROUNDINGS as $key => $rounding)
            $roundings[$key] = $rounding['name'];

        return $roundings;
    }

    private function _getOrganizations()
    {
        $organizations = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $organizationsOptions = array(0 => 'All');
        foreach($organizations as $organization)
            $organizationsOptions[$organization->getId()] = $organization->getName();

        return $organizationsOptions;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'value',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new PriceValidator(),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'method',
                    'required' => true,
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'name',
                    'required' => true,
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'rounding',
                    'required' => true,
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'type',
                    'required' => true,
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'organization',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}
