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

use CommonBundle\Component\OldForm\Bootstrap\Element\Password,
    CommonBundle\Component\OldForm\Bootstrap\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Account activate form.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Activate extends \CommonBundle\Component\OldForm\Bootstrap\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $field = new Password('credential');
        $field->setLabel('Password')
            ->setRequired();
        $this->add($field);

        $field = new Password('verify_credential');
        $field->setLabel('Verify Password')
            ->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Activate');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'credential',
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
                    'name'     => 'verify_credential',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'identical',
                            'options' => array(
                                'token' => 'credential',
                            ),
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
