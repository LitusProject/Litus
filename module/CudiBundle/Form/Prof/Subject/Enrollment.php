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

namespace CudiBundle\Form\Prof\Subject;

use SyllabusBundle\Entity\StudentEnrollment;

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

        $this->add(array(
            'type'       => 'text',
            'name'       => 'students',
            'label'      => 'Students',
            'required'   => true,
            'value'      => null !== $this->enrollment ? $enrollment->getNumber() : '',
            'attributes' => array(
                'autocomplete' => 'off',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                ),
            ),
        ));

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
