<?php

namespace SyllabusBundle\Form\Admin\Subject\Comment;

/**
 * Add Comment
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\Subject\Comment';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'textarea',
                'name'     => 'text',
                'label'    => 'Comment',
                'required' => true,
                'options'  => array(
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
                'name'       => 'type',
                'label'      => 'Type',
                'required'   => true,
                'attributes' => array(
                    'options' => array(
                        'internal' => 'Internal',
                        'external' => 'External',
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'comment_add');
    }
}
