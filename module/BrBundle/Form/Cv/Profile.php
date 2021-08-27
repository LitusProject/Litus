<?php

namespace BrBundle\Form\Cv;

/**
 * Upload Profile Image
 *
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 */
class Profile extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->setAttribute('class', 'form-inline');

        $this->add(
            array(
                'type'       => 'hidden',
                'name'       => 'x',
                'required'   => false,
                'value'      => 0,
                'attributes' => array(
                    'id' => 'x',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'hidden',
                'name'       => 'y',
                'required'   => false,
                'value'      => 0,
                'attributes' => array(
                    'id' => 'y',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'hidden',
                'name'       => 'x2',
                'required'   => false,
                'value'      => 0,
                'attributes' => array(
                    'id' => 'x2',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'hidden',
                'name'       => 'y2',
                'required'   => false,
                'value'      => 0,
                'attributes' => array(
                    'id' => 'y2',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'hidden',
                'name'       => 'w',
                'required'   => false,
                'value'      => 0,
                'attributes' => array(
                    'id' => 'w',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'hidden',
                'name'       => 'h',
                'required'   => false,
                'value'      => 0,
                'attributes' => array(
                    'id' => 'h',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'file',
                'name'       => 'profile',
                'required'   => false,
                'attributes' => array(
                    'data-type' => 'small',
                ),
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'FileExtension',
                                'options' => array(
                                    'extension' => 'jpg,png',
                                ),
                            ),
                            array(
                                'name'    => 'FileSize',
                                'options' => array(
                                    'max' => '5MB',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );
    }
}
