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

use CommonBundle\Component\OldForm\Bootstrap\Element\Reset,
    CommonBundle\Component\OldForm\Bootstrap\Element\Button,
    CommonBundle\Component\OldForm\Bootstrap\Element\Text;

/**
 * Sign in to queue
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SignIn extends \CommonBundle\Component\OldForm\Bootstrap\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $field = new Text('username');
        $field->setLabel('Student Number')
            ->setRequired()
            ->setAttribute('id', 'username')
            ->setAttribute('placeholder', "Student Number")
            ->setAttribute('autocomplete', 'off');
        $this->add($field);

        $field = new Button('submit');
        $field->setLabel('Sign In')
            ->setAttribute('id', 'signin');
        $this->add($field);

        $field = new Reset('cancel');
        $field->setValue('Cancel');
        $this->add($field);
    }
}
