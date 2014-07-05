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

namespace MailBundle\Form\Admin\Cudi;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
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
     * @param string          $subject
     * @param string          $message
     * @param integer         $semester
     * @param null|string|int $name     Optional name for the element
     */
    public function __construct($subject, $message, $semester, $name = null)
    {
        parent::__construct($name);

        $field = new Text('subject');
        $field->setLabel('Subject')
            ->setAttribute('style', 'width: 400px;')
            ->setRequired()
            ->setValue($subject);
        $this->add($field);

        $field = new Select('semester');
        $field->setLabel('Semester')
        ->setAttribute('options', array(1 => 'First Semester', 2 => 'Second Semester'))
            ->setRequired()
            ->setValue($semester);
        $this->add($field);

        $field = new Textarea('message');
        $field->setLabel('Message')
            ->setRequired()
            ->setValue($message);
        $this->add($field);

        $field = new Checkbox('test_it');
        $field->setLabel('Send Test to System Administrator')
            ->setValue(true);
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Send Mail')
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
                    'name'     => 'semester',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'inArray',
                            'options' => array(
                                'haystack' => array(1, 2),
                            ),
                        )
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'message',
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
