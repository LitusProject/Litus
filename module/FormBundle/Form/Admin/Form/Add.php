<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace FormBundle\Form\Admin\Form;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Tabs,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabPane,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Node\Form,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Select('type');
        $field->setLabel('Type')
            ->setAttribute('options', array('form' => 'Form', 'doodle' => 'Doodle'))
            ->setAttribute('class', 'form doodle')
            ->setRequired();
        $this->add($field);

        $tabs = new Tabs('languages');
        $tabs->setAttribute('class', 'form doodle');
        $this->add($tabs);

        $tabContent = new TabContent('tab_content');

        foreach($this->getLanguages() as $language) {
            $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));

            $pane = new TabPane('tab_' . $language->getAbbrev());

            $field = new Text('title_' . $language->getAbbrev());
            $field->setLabel('Title')
                ->setAttribute('class', 'form doodle')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $field = new Textarea('introduction_' . $language->getAbbrev());
            $field->setLabel('Introduction')
                ->setAttribute('class', 'md form doodle')
                ->setAttribute('rows', 20)
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $field = new Text('submittext_' . $language->getAbbrev());
            $field->setLabel('Submit Button Text')
                ->setAttribute('class', 'form doodle')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $field = new Text('updatetext_' . $language->getAbbrev());
            $field->setLabel('Update Button Text')
                ->setAttribute('class', 'form doodle')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $tabContent->add($pane);
        }

        $this->add($tabContent);

        $field = new Text('start_date');
        $field->setLabel('Start Date')
            ->setAttribute('class', 'form doodle')
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('data-timepicker', true)
            ->setRequired();
        $this->add($field);

        $field = new Text('end_date');
        $field->setLabel('End Date')
            ->setAttribute('class', 'form doodle')
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('data-timepicker', true)
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('active');
        $field->setLabel('Active')
            ->setAttribute('class', 'form doodle');
        $this->add($field);

        $field = new Text('max');
        $field->setLabel('Total Max Entries')
            ->setAttribute('class', 'form');
        $this->add($field);

        $field = new Checkbox('non_members');
        $field->setLabel('Allow Entry Without Login')
            ->setAttribute('class', 'form doodle');
        $this->add($field);

        $field = new Checkbox('editable_by_user');
        $field->setLabel('Allow Users To Edit Their Info')
            ->setAttribute('class', 'form doodle');
        $this->add($field);

        $field = new Checkbox('names_visible_for_others');
        $field->setLabel('Names Are Visible For Others')
            ->setAttribute('class', 'doodle');
        $this->add($field);

        $field = new Checkbox('multiple');
        $field->setLabel('Multiple Entries per Person')
            ->setAttribute('class', 'form doodle');
        $this->add($field);

        $field = new Checkbox('mail');
        $field->setLabel('Send Confirmation Mail')
            ->setAttribute('class', 'form doodle');
        $this->add($field);

        $mail = new Collection('mail_form');
        $mail->setLabel('Mail')
            ->setAttribute('id', 'mail_form')
            ->setAttribute('class', 'form doodle');
        $this->add($mail);

        $field = new Text('mail_from');
        $field->setLabel('Mail Sender Address')
            ->setAttribute('class', 'form doodle')
            ->setRequired();
        $mail->add($field);

        $field = new Checkbox('mail_bcc');
        $field->setLabel('Send BCC to sender for every entry')
            ->setAttribute('class', 'form doodle')
            ->setValue(true);
        $mail->add($field);

        $field = new Text('mail_subject');
        $field->setLabel('Subject')
            ->setAttribute('class', 'form doodle')
            ->setRequired();
        $mail->add($field);

        $field = new Textarea('mail_body');
        $field->setLabel('Body')
            ->setAttribute('class', 'form doodle')
            ->setAttribute('rows', 20)
            ->setValue('Example mail:

Dear %first_name% %last_name%,

Your subscription was successful. Your unique subscription id is %id%. Below is a summary of the values you entered in this form:

%entry_summary%

With best regards,
The Form Creator')
            ->setRequired();
        $mail->add($field);

        $field = new Checkbox('reminder_mail');
        $field->setLabel('Send Reminder Mail')
            ->setAttribute('class', 'doodle');
        $this->add($field);

        $reminder = new Collection('reminder_mail_form');
        $reminder->setLabel('Reminder Mail')
            ->setAttribute('id', 'reminder_mail_form')
            ->setAttribute('class', 'doodle');
        $this->add($reminder);

        $field = new Text('reminder_mail_from');
        $field->setLabel('Mail Sender Address')
            ->setAttribute('class', 'doodle')
            ->setRequired();
        $reminder->add($field);

        $field = new Checkbox('reminder_mail_bcc');
        $field->setLabel('Send BCC to sender for every entry')
            ->setAttribute('class', 'doodle')
            ->setValue(true);
        $reminder->add($field);

        $field = new Text('reminder_mail_subject');
        $field->setLabel('Subject')
            ->setAttribute('class', 'doodle')
            ->setRequired();
        $reminder->add($field);

        $field = new Textarea('reminder_mail_body');
        $field->setLabel('Body')
            ->setAttribute('class', 'doodle')
            ->setAttribute('rows', 20)
            ->setValue('Example mail:

Dear %first_name% %last_name%,

Your subscription was successful. Your unique subscription id is %id%. Below is a summary of the values you entered in this form:

%entry_summary%

With best regards,
The Form Creator')
            ->setRequired();
        $reminder->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'form_add');
        $this->add($field);
    }

    protected function getLanguages()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'start_date',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'end_date',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                        new DateCompareValidator('start_date', 'd/m/Y H:i'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'max',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'digits',
                        ),
                    ),
                )
            )
        );

        if ($this->data['mail']) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'mail_from',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'EmailAddress',
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'mail_subject',
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
                        'name'     => 'mail_body',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );
        }

        if (isset($this->data['reminder_mail']) && $this->data['reminder_mail']) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'reminder_mail_from',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'EmailAddress',
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'reminder_mail_subject',
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
                        'name'     => 'reminder_mail_body',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );
        }

        foreach($this->getLanguages() as $language) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'title_' . $language->getAbbrev(),
                        'required' => $language->getAbbrev() == \Locale::getDefault(),
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'introduction_' . $language->getAbbrev(),
                        'required' => $language->getAbbrev() == \Locale::getDefault(),
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'submittext_' . $language->getAbbrev(),
                        'required' => $language->getAbbrev() == \Locale::getDefault(),
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'updatetext_' . $language->getAbbrev(),
                        'required' => $language->getAbbrev() == \Locale::getDefault(),
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );
        }

        return $inputFilter;
    }
}
