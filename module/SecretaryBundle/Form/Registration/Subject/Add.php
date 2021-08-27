<?php

namespace SecretaryBundle\Form\Registration\Subject;

/**
 * Add Subject
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'subject',
                'label'    => 'Subject',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadSubject'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add Other Subject', '', 'add_subject');
    }
}
