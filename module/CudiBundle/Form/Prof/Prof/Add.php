<?php

namespace CudiBundle\Form\Prof\Prof;

/**
 * Add Prof
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
                'type'       => 'typeahead',
                'name'       => 'prof',
                'label'      => 'Docent',
                'required'   => true,
                'attributes' => array(
                    'id' => 'prof',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add');
    }
}
