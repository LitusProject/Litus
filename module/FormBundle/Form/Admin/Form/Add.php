<?php

namespace FormBundle\Form\Admin\Form;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;

/**
 * Add Form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    protected $hydrator = 'FormBundle\Hydrator\Node\Form';

    public function init()
    {
        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'type',
                'label'      => 'Type',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'form_type',
                    'options' => array('form' => 'Form', 'doodle' => 'Doodle'),
                ),
            )
        );

        parent::init();

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'start_date',
                'label'    => 'Start Date',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'end_date',
                'label'    => 'End Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'start_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'active',
                'label' => 'Active',
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'max',
                'label'      => 'Total Maximum Entries',
                'attributes' => array(
                    'class'     => 'form_element',
                    'data-help' => 'The maximum number of form submittions possible.',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'non_member',
                'label'      => 'Without Login',
                'attributes' => array(
                    'data-help' => 'Allow users to submit this form without login. A name and email field will be added as first fields of this form.',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'editable_by_user',
                'label'      => 'Editable By User',
                'attributes' => array(
                    'data-help' => 'The users are allowed to edit the info of previously submitted entries. This will also enable the "Save as Concept" button.',
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'student_info',
                'label'    => 'Ask Student Info',
                'required' => false,
                'value'    => true,
            ),
        );


        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'send_guest_login_mail',
                'label'      => 'Send Guest Login Mail',
                'attributes' => array(
                    'id'        => 'send_guest_login_mail',
                    'data-help' => 'Send a mail to guests after submitting form to login later and edit/view their submission.<br>For this option the confirmation mail must be enabled!',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'names_visible_for_others',
                'label'      => 'Names Visible For Others',
                'attributes' => array(
                    'class'     => 'doodle_element',
                    'data-help' => 'Display the name of other person registered for slots in this doodle.',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'multiple',
                'label'      => 'Multiple Entries Per Person',
                'attributes' => array(
                    'data-help' => 'The maximum number of form submittions possible for each user.',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'mail',
                'label'      => 'Send Confirmation Mail',
                'attributes' => array(
                    'id' => 'mail',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'form_form_mail_add',
                'name'       => 'mail_form',
                'label'      => 'Mail',
                'required'   => true,
                'attributes' => array(
                    'id' => 'mail_form',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'reminder_mail',
                'label'      => 'Send Reminder Mail',
                'attributes' => array(
                    'id'        => 'reminder_mail',
                    'class'     => 'doodle_element',
                    'data-help' => 'This mail will be sent the day before the timeslot starts. <br><br> If the slot is on Tuesday, the user will receive an email on Sunday morning.',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'form_form_mail_add',
                'name'       => 'reminder_mail_form',
                'label'      => 'Reminder Mail',
                'required'   => true,
                'attributes' => array(
                    'id' => 'reminder_mail_form',
                ),
            )
        );

        $this->addSubmit('Add', 'form_add');

        $hydrator = $this->getHydrator();
        $this->populateValues($hydrator->extract(null));
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
                'type'       => 'text',
                'name'       => 'title',
                'label'      => 'Title',
                'required'   => $isDefault,
                'attributes' => array(
                    'width' => '400px',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $container->add(
            array(
                'type'       => 'textarea',
                'name'       => 'introduction',
                'label'      => 'Introduction',
                'required'   => $isDefault,
                'attributes' => array(
                    'class' => 'md',
                    'row'   => 20,
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $container->add(
            array(
                'type'     => 'text',
                'name'     => 'submittext',
                'label'    => 'Submit Button Text',
                'required' => $isDefault,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $container->add(
            array(
                'type'     => 'text',
                'name'     => 'updatetext',
                'label'    => 'Update Button Text',
                'required' => $isDefault,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        if (!$this->get('mail')->getValue()) {
            unset($specs['mail_form']);
        } else {
            unset($specs['mail_form']['languages']);
        }

        if (!$this instanceof Edit || $this->isDoodle()) {
            if (!$this->get('reminder_mail')->getValue()) {
                unset($specs['reminder_mail_form']);
            }
        }

        return $specs;
    }
}
