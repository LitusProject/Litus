<?php

namespace SyllabusBundle\Form\Admin\Academic\Study;

/**
 * Add Study
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
                'name'       => 'study',
                'label'      => 'Study',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 500px',
                ),
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadStudy'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }
}
