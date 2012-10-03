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

use CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\Barcode as BarcodeValidator,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\Users\Person,
    CommonBundle\Entity\Users\Statuses\Organization as OrganizationStatus,
    CommonBundle\Entity\Users\Statuses\University as UniversityStatus,
    Doctrine\ORM\EntityManager,
    SecretaryBundle\Component\Validator\Barcode as BarcodeValidator,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit Academic
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \CommonBundle\Form\Admin\Person\Edit
{
    /**
     * @var \CommonBundle\Entity\Users\Person The person we're going to modify
     */
    private $_person = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The academic year
     * @param \CommonBundle\Entity\Users\Person $person The person we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, AcademicYear $academicYear, Person $person, $name = null)
    {
        parent::__construct($entityManager, $person, $name);

        $this->_person = $person;

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

        $field = new Text('barcode');
        $field->setLabel('Barcode')
            ->setAttribute('class', 'disableEnter');
        $collection->add($field);

        $collection = new Collection('university');
        $collection->setLabel('University');
        $this->add($collection);

        $field = new Text('university_identification');
        $field->setLabel('Identification');
        $collection->add($field);

        $field = new Select('university_status');
        $field->setLabel('Status')
            ->setRequired()
            ->setAttribute('options', UniversityStatus::$possibleStatuses);
        $collection->add($field);

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'academic_edit');
        $this->add($field);

        $this->setData(
            array(
                'organization_status' => $person->getOrganizationStatus($academicYear) ? $person->getOrganizationStatus($academicYear)->getStatus() : null,
                'barcode' => $person->getBarcode() ? $person->getBarcode()->getBarcode() : '',
                'university_identification' => $person->getUniversityIdentification(),
                'university_status' => $person->getUniversityStatus($academicYear) ? $person->getUniversityStatus($academicYear)->getStatus() : null,
            )
        );
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = parent::getInputFilter();
            $factory = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'barcode',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'barcode',
                                'options' => array(
                                    'adapter'     => 'Ean12',
                                    'useChecksum' => false,
                                ),
                            ),
                            new BarcodeValidator($this->_entityManager, $this->_person),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'university_identification',
                        'required' => false,
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
