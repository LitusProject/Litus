<?php

namespace CommonBundle\Form\Admin\Unit;

/**
 * The form used to add an external member to a unit.
 *
 */
class External extends \CommonBundle\Component\Form\Admin\Form
{
    const FILE_SIZE = '10MB';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'first_name',
                'label'    => 'First Name',
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
                'type'     => 'text',
                'name'     => 'last_name',
                'label'    => 'Last Name',
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
                'type'  => 'checkbox',
                'name'  => 'coordinator',
                'label' => 'Coordinator',
            )
        );

        $this->add(
            array(
                'type'  => 'text',
                'name'  => 'description',
                'label' => 'Description',
            )
        );

        $this->add(
            array(
                'type'       => 'file',
                'name'       => 'picture',
                'label'      => 'Picture',
                'required'   => true,
                'attributes' => array(
                    'data-help' => 'The picture must be an image with a file size limit of ' . self::FILE_SIZE . '.',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name' => 'FileIsImage',
                            ),
                            array(
                                'name'    => 'FileSize',
                                'options' => array(
                                    'max' => self::FILE_SIZE,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'hidden',
                'name'  => 'mapType',
                'value' => 'external',
            )
        );

        $this->addSubmit('Add', 'unit_add');
    }
}
