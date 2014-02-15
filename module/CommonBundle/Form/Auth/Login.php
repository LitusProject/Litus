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

namespace CommonBundle\Form\Auth;

use CommonBundle\Component\Form\Bootstrap\Element\Checkbox,
    CommonBundle\Component\Form\Bootstrap\Element\Password,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Authentication login form.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Login extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @param string $action
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($action = '', $name = null)
    {
        parent::__construct($name);

        $this->setAttribute('action', $action);

        $this->setAttribute('id', 'login')
            ->setAttribute('class', 'form-horizontal');

        $field = new Text('username');
        $field->setLabel('Username')
            ->setRequired();
        $this->add($field);

        $field = new Password('password');
        $field->setLabel('Password')
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('remember_me');
        $field->setLabel('Remember Me');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Login')
            ->setAttribute('class', 'btn btn-default pull-right');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'username',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'password',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
