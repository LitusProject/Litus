<?php

namespace CudiBundle\Form\Prof\Comment;

/**
 * Add Reply
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Reply extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'hidden',
                'name'       => 'comment',
                'attributes' => array(
                    'id' => 'comment',
                ),
                'options'    => array(
                    'input' => array(
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'Textarea',
                'name'       => 'reply',
                'label'      => 'Reply',
                'required'   => true,
                'attributes' => array(
                    'rows' => 5,
                    'id'   => 'reply',
                ),
                'options'    => array(
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
