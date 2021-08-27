<?php

namespace CudiBundle\Form\Prof\Comment;

/**
 * Add Comment
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
                'type'       => 'textarea',
                'name'       => 'text',
                'label'      => 'Comment',
                'required'   => true,
                'attributes' => array(
                    'rows' => 5,
                    'id'   => 'text',
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
    }
}
