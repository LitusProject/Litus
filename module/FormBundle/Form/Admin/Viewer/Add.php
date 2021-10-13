<?php

namespace FormBundle\Form\Admin\Viewer;

/**
 * Add Viewer
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'FormBundle\Hydrator\ViewerMap';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'person',
                'label'    => 'Person',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'edit',
                'label' => 'Can Edit/Delete entries',
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'mail',
                'label' => 'Can Mail Participants',
            )
        );

        $this->addSubmit('Add', 'add');
    }
}
