<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

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
