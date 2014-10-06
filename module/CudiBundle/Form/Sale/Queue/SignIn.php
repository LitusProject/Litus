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

namespace CudiBundle\Form\Sale\Queue;

/**
 * Sign in to queue
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SignIn extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'text',
            'name'       => 'username',
            'label'      => 'Student Number',
            'required'   => true,
            'attributes' => array(
                'autocomplete' => 'off',
                'id'           => 'username',
                'placeholder'  => 'Student Number',
            ),
        ));

        $this->add(array(
            'type'       => 'button',
            'name'       => 'submit',
            'label'      => 'Sign In',
            'attributes' => array(
                'id' => 'signin',
            ),
        ));

        $this->add(array(
            'type'  => 'reset',
            'name'  => 'cancel',
            'label' => 'Cancel',
        ));
    }
}
