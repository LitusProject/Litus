<?php

namespace CudiBundle\Form\Prof\Mapping;

/**
 * Add Mapping
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
                'name'       => 'article',
                'label'      => 'Article',
                'required'   => true,
                'attributes' => array(
                    'id' => 'article',
                ),
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadArticle'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'mandatory',
                'label' => 'Mandatory',
            )
        );

        $this->addSubmit('Add');
    }
}
