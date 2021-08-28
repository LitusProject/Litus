<?php

namespace CudiBundle\Form\Prof\Subject;

use SyllabusBundle\Entity\Subject\StudentEnrollment;

/**
 * Update student enrollment
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Enrollment extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var StudentEnrollment|null
     */
    private $enrollment;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'students',
                'label'      => 'Students',
                'required'   => true,
                'value'      => $this->enrollment !== null ? $this->enrollment->getNumber() : '',
                'attributes' => array(
                    'autocomplete' => 'off',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Update');
    }

    /**
     * @param  StudentEnrollment|null $enrollment
     * @return self
     */
    public function setEnrollment(StudentEnrollment $enrollment = null)
    {
        $this->enrollment = $enrollment;

        return $this;
    }
}
