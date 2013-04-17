<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Form\Admin\Volunteer;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Bootstrap\Element\File,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Form\Admin\Element\Select,
    MailBundle\Component\Validator\MultiMail as MultiMailValidator,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setAttribute('accept-charset', 'utf-8');

        $field = new Text('from');
        $field->setLabel('From')
            ->setAttribute('style', 'width: 400px;')
            ->setRequired();
        $this->add($field);

        $field = new Text('subject');
        $field->setLabel('Subject')
            ->setAttribute('style', 'width: 400px;');
        $this->add($field);

        $field = new Textarea('message');
        $field->setLabel('Message')
            ->setAttribute('style', 'width: 500px; height: 200px;');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Send')
            ->setAttribute('class', 'mail');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'subject',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'message',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'from',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'emailAddress',
                        )
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
