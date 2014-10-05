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
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Form\Account;

/**
 * Upload Profile Image
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Profile extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->setAttribute('class', 'form-inline');

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'x',
            'required'   => false,
            'value'      => 0,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'y',
            'required'   => false,
            'value'      => 0,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'x2',
            'required'   => false,
            'value'      => 0,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'y2',
            'required'   => false,
            'value'      => 0,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'w',
            'required'   => false,
            'value'      => 0,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'h',
            'required'   => false,
            'value'      => 0,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'file',
            'name'       => 'profile',
            'required'   => false,
            'attributes' => array(
                'data-type' => 'small',
            ),
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name' => 'fileextension',
                            'options' => array(
                                'extension' => 'jpg,png',
                            ),
                        ),
                        array(
                            'name' => 'filefilessize',
                            'options' => array(
                                'max' => '2MB',
                            ),
                        ),
                    ),
                ),
            ),
        ));
    }
}
