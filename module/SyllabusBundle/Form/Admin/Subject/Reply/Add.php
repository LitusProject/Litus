<?php

namespace SyllabusBundle\Form\Admin\Subject\Reply;

/**
 * Add Reply
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\Subject\Reply';

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

        $this->addSubmit('Add', 'comment_add');
    }
}
