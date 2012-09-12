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
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\Users\Person,
    CommonBundle\Entity\Users\Statuses\University,
    Doctrine\ORM\EntityManager,
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
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\Users\Person $person The person we're going to modify
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The academic year
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, AcademicYear $academicYear, Person $person, $name = null)
    {
        parent::__construct($entityManager, $person, $name);

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
            ->setAttribute('options', University::$possibleStatuses);
        $collection->add($field);

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'academic_edit');
        $this->add($field);

        $this->setData(
            array(
                'organization_status' => $person->getOrganizationStatus($academicYear) ? $person->getOrganizationStatus($academicYear)->getStatus() : null,
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
