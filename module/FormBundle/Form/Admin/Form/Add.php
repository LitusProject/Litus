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

namespace FormBundle\Form\Admin\Form;

use CommonBundle\Component\OldForm\Admin\Element\Checkbox,
    CommonBundle\Component\OldForm\Admin\Element\Collection,
    CommonBundle\Component\OldForm\Admin\Element\Select,
    CommonBundle\Component\OldForm\Admin\Element\Tabs,
    CommonBundle\Component\OldForm\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\OldForm\Admin\Form\SubForm\TabPane,
    CommonBundle\Component\OldForm\Admin\Element\Text,
    CommonBundle\Component\OldForm\Admin\Element\Textarea,
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
class Add extends \CommonBundle\Component\OldForm\Admin\Form\Tabbable
{
    /**
     * @var EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param null|string|int $name          Optional name for the element
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

        foreach ($this->getLanguages() as $language) {
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
            ->setAttribute('class', 'form')
            ->setAttribute('data-help', 'The maximum number of form submittions possible.');
        $this->add($field);

        $field = new Checkbox('non_members');
        $field->setLabel('Allow Entry Without Login')
            ->setAttribute('class', 'form doodle')
            ->setAttribute('data-help', 'Allow users to submit this form without login. A name and email field will be added as first fields of this form.');
        $this->add($field);

        $field = new Checkbox('editable_by_user');
        $field->setLabel('Allow Users To Edit Their Info')
            ->setAttribute('class', 'form doodle')
            ->setAttribute('data-help', 'The users are allowed to edit the info of previously submitted entries. This will also enable the "Save as Concept" button.');
        $this->add($field);

        $field = new Checkbox('send_guest_login_mail');
        $field->setLabel('Send Guest Login Mail')
            ->setAttribute('class', 'form doodle')
            ->setAttribute('data-help', 'Send a mail to guests after submitting form to login later and edit/view their submission.<br>For this option the confirmation mail must be enabled!');
        $this->add($field);

        $field = new Checkbox('names_visible_for_others');
        $field->setLabel('Names Are Visible For Others')
            ->setAttribute('class', 'doodle')
            ->setAttribute('data-help', 'Display the name of other person registered for slots in this doodle.');
        $this->add($field);

        $field = new Checkbox('multiple');
        $field->setLabel('Multiple Entries Per Person')
            ->setAttribute('class', 'form doodle')
            ->setAttribute('data-help', 'The maximum number of form submittions possible for each user.');
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

        $mailTabs = new Tabs('mail_languages');
        $mail->add($mailTabs);

        $mailTabContent = new TabContent('mail_tab_content');

        $mailTemplate = unserialize(
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('form.mail_confirmation')
        );

        foreach ($this->getLanguages() as $language) {
            $mailTabs->addTab(array($language->getName() => '#mail_tab_' . $language->getAbbrev()));

            $pane = new TabPane('mail_tab_' . $language->getAbbrev());

            $field = new Text('mail_subject_' . $language->getAbbrev());
            $field->setLabel('Subject')
                ->setAttribute('class', 'form doodle')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $field = new Textarea('mail_body_' . $language->getAbbrev());
            $field->setLabel('Body')
                ->setAttribute('class', 'form doodle')
                ->setAttribute('rows', 20)
                ->setRequired($language->getAbbrev() == \Locale::getDefault())
                ->setValue(isset($mailTemplate[$language->getAbbrev()]) ? $mailTemplate[$language->getAbbrev()]['content'] : '');
            $pane->add($field);

            $mailTabContent->add($pane);
        }

        $mail->add($mailTabContent);

        $field = new Checkbox('reminder_mail');
        $field->setLabel('Send Reminder Mail')
            ->setAttribute('class', 'doodle')
            ->setAttribute('data-help', 'This mail will be sent the day before the timeslot starts. <br><br> If the slot is on Tuesday, the user will receive an email on Sunday morning.');
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

        $reminderMailTabs = new Tabs('reminder_mail_languages');
        $reminder->add($reminderMailTabs);

        $reminderMailTabContent = new TabContent('reminder_mail_tab_content');

        $reminderMailTemplate = unserialize(
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('form.mail_reminder')
        );

        foreach ($this->getLanguages() as $language) {
            $reminderMailTabs->addTab(array($language->getName() => '#reminder_mail_tab_' . $language->getAbbrev()));

            $pane = new TabPane('reminder_mail_tab_' . $language->getAbbrev());

            $field = new Text('reminder_mail_subject_' . $language->getAbbrev());
            $field->setLabel('Subject')
                ->setAttribute('class', 'doodle')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $field = new Textarea('reminder_mail_body_' . $language->getAbbrev());
            $field->setLabel('Body')
                ->setAttribute('class', 'doodle')
                ->setAttribute('rows', 20)
                ->setRequired($language->getAbbrev() == \Locale::getDefault())
                ->setValue(isset($reminderMailTemplate[$language->getAbbrev()]) ? $reminderMailTemplate[$language->getAbbrev()]['content'] : '');
            $pane->add($field);

            $reminderMailTabContent->add($pane);
        }

        $reminder->add($reminderMailTabContent);

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

            foreach ($this->getLanguages() as $language) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'mail_subject_' . $language->getAbbrev(),
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
                            'name'     => 'mail_body_' . $language->getAbbrev(),
                            'required' => $language->getAbbrev() == \Locale::getDefault(),
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        )
                    )
                );
            }
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

            foreach ($this->getLanguages() as $language) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'reminder_mail_subject_' . $language->getAbbrev(),
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
                            'name'     => 'reminder_mail_body_' . $language->getAbbrev(),
                            'required' => $language->getAbbrev() == \Locale::getDefault(),
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        )
                    )
                );
            }
        }

        foreach ($this->getLanguages() as $language) {
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
