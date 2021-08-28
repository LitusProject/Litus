<?php

namespace SyllabusBundle\Form\Admin\Subject;

use SyllabusBundle\Entity\Subject;

/**
 * Add Subject
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\Subject';

    /**
     * @var Subject|null
     */
    protected $subject = null;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'code',
                'label'    => 'Code',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'SubjectCode',
                                'options' => array(
                                    'exclude' => $this->subject,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'name',
                'label'      => 'Name',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 400px;',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'semester',
                'label'      => 'Semester',
                'required'   => true,
                'attributes' => array(
                    'options' => array(
                        '1' => 'First Semester',
                        '2' => 'Second Semester',
                        '3' => 'Both Semesters',
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'credits',
                'label'    => 'Credits',
                'required' => true,
                'options'  => array(
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

        $this->addSubmit('Add', 'add');
    }

    /**
     * @param  Subject $subject
     * @return self
     */
    public function setSubject(Subject $subject)
    {
        $this->subject = $subject;

        return $this;
    }
}
