<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Sale\Queue;

use CommonBundle\Component\Form\Bootstrap\Element\Reset,
    CommonBundle\Component\Form\Bootstrap\Element\Button,
    CommonBundle\Component\Form\Bootstrap\Element\Text;

/**
 * Sign in to queue
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SignIn extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null )
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
