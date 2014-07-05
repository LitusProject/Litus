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

namespace SyllabusBundle\Form\Admin\Subject\Study;

use CommonBundle\Component\OldForm\Admin\Element\Hidden,
    CommonBundle\Component\OldForm\Admin\Element\Checkbox,
    CommonBundle\Component\OldForm\Admin\Element\Text,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager,
    SyllabusBundle\Component\Validator\Subject\Study as StudyValidator,
    SyllabusBundle\Entity\Subject,
    SyllabusBundle\Entity\StudySubjectMap,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Study to Subject
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\OldForm\Admin\Form
{
    /**
     * @var EntityManager The EntityManager instance
     */
    protected $_entityManager;

    /**
     * @var Subject
     */
    protected $_subject;

    /**
     * @var AcademicYear
     */
    protected $_academicYear;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param Subject         $subject
     * @param AcademicYear    $academicYear
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Subject $subject, AcademicYear $academicYear, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_subject = $subject;
        $this->_academicYear = $academicYear;

        $field = new Hidden('study_id');
        $field->setAttribute('id', 'studyId');
        $this->add($field);

        $field = new Text('study');
        $field->setLabel('Study')
            ->setAttribute('style', 'width: 400px;')
            ->setAttribute('id', 'studySearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead')
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('mandatory');
        $field->setLabel('Mandatory');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'add');
        $this->add($field);
    }

    public function populateFromMapping(StudySubjectMap $mapping)
    {
        $this->setData(
            array(
                'mandatory' => $mapping->isMandatory(),
            )
        );
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'study_id',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'study',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new StudyValidator($this->_entityManager, $this->_subject, $this->_academicYear),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
