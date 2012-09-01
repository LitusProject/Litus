<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Prof\Subject;

use CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    SyllabusBundle\Entity\StudentEnrollment,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Update student enrollment
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Enrollment extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @param \SyllabusBundle\Entity\StudentEnrollment $enrollment
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(StudentEnrollment $enrollment = null, $name = null)
    {
        parent::__construct($name);

        $field = new Text('students');
        $field->setLabel('Students')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setAttribute('autocomplete', 'off')
            ->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Update');
        $this->add($field);

        if (isset($enrollment)) {
            $this->setData(
                array(
                    'students' => $enrollment->getNumber(),
                )
            );
        }
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'students',
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

            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
