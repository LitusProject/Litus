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

namespace MailBundle\Form\Admin\Study;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\File,
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
    public function __construct($studies, $groups, $storedMessages, $name = null)
    {
        parent::__construct($name);

        $this->setAttribute('id', 'uploadFile');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('accept-charset', 'utf-8');

        $studyNames = array();
        foreach($studies as $study)
            $studyNames[$study->getId()] = 'Phase ' . $study->getPhase() . ' - ' . $study->getFullTitle();

        $groupNames = array();
        foreach($groups as $group)
            $groupNames[$group->getId()] = $group->getName();

        $storedMessagesTitles = array(
            '' => ''
        );
        foreach ($storedMessages as $storedMessage)
            $storedMessagesTitles[$storedMessage->getId()] = '(' . $storedMessage->getCreationTime()->format('d/m/Y') . ') ' . $storedMessage->getSubject();

        if (0 != count($studyNames)) {
            $field = new Select('studies');
            $field->setLabel('Studies')
                ->setAttribute('multiple', true)
                ->setAttribute('style', 'max-width: 100%;')
                ->setAttribute('options', $studyNames);
            $this->add($field);
        }

        if (0 != count($groupNames)) {
            $field = new Select('groups');
            $field->setLabel('Groups')
                ->setAttribute('multiple', true)
                ->setAttribute('options', $groupNames);
            $this->add($field);
        }

        $field = new Checkbox('test');
        $field->setLabel('Test Mail');
        $this->add($field);

        $field = new Checkbox('html');
        $field->setLabel('HTML Mail');
        $this->add($field);

        $field = new Text('from');
        $field->setLabel('From')
            ->setAttribute('style', 'width: 400px;')
            ->setRequired();
        $this->add($field);

        $field = new Text('bcc');
        $field->setLabel('Additional BCC')
            ->setAttribute('style', 'width: 400px;');
        $this->add($field);

        if (0 != count($storedMessages)) {
            $collection = new Collection('select_message');
            $collection->setLabel('Select Message');
            $this->add($collection);

            $field = new Select('stored_message');
            $field->setLabel('Stored Message')
                ->setAttribute('style', 'max-width: 100%;')
                ->setAttribute('options', $storedMessagesTitles);
            $collection->add($field);
        }

        if (0 != count($storedMessages)) {
            $collection = new Collection('compose_message');
            $collection->setLabel('Compose Message');
            $this->add($collection);
        } else {
            $collection = $this;
        }

        $field = new Text('subject');
        $field->setLabel('Subject')
            ->setAttribute('style', 'width: 400px;');
        $collection->add($field);

        $field = new Textarea('message');
        $field->setLabel('Message')
            ->setAttribute('style', 'width: 500px; height: 200px;');
        $collection->add($field);

        $field = new File('file');
        $field->setLabel('Attachments')
            ->setAttribute('multiple', 'multiple')
            ->setRequired();
        $collection->add($field);

        $field = new Submit('send');
        $field->setValue('Send')
            ->setAttribute('id', 'send_mail')
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

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'bcc',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new MultiMailValidator()
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'file',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'filefilessize',
                            'options' => array(
                                'max' => '50MB',
                            ),
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
