<?php

namespace SyllabusBundle\Form\Admin\Subject\Prof;

/**
 * Add Prof
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'prof',
                'label'      => 'Prof',
                'required'   => true,
                'attributes' => array(
                    'size' => 70,
                ),
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'docent_add');
    }
}
