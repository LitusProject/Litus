<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace CommonBundle\Form\Admin\Academic;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Entity\Users\Statuses\Organization as OrganizationStatus,
    CommonBundle\Entity\Users\Statuses\University as UniversityStatus,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Academic
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Form\Admin\Person\Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($entityManager, $name);

        $field = new Checkbox('activation_code');
        $field->setLabel('Activation Code');
        $this->add($field);

        $collection = new Collection('organization');
        $collection->setLabel('Organization');
        $this->add($collection);

        $field = new Select('organization_status');
        $field->setLabel('Status')
            ->setAttribute(
                'options',
                array_merge(
                    array(
                        '' => ''
                    ),
                    OrganizationStatus::$possibleStatuses
                )
            );
        $collection->add($field);

        $collection = new Collection('university');
        $collection->setLabel('University');
        $this->add($collection);

        $field = new Text('university_identification');
        $field->setLabel('Identification')
            ->setRequired();
        $collection->add($field);

        $field = new Select('university_status');
        $field->setLabel('Status')
            ->setRequired()
            ->setAttribute('options', UniversityStatus::$possibleStatuses);
        $collection->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'academic_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = parent::getInputFilter();
            $factory = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'university_identification',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'alnum'
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'university_status',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
