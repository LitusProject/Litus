<?php

namespace MailBundle\Form\Admin\Prof;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $message;

    /**
     * @var integer
     */
    private $semester;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'subject',
                'label'      => 'Subject',
                'required'   => true,
                'value'      => $this->getSubject(),
                'attributes' => array(
                    'style' => 'width: 400px;',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'select',
                'name'     => 'semester',
                'label'    => 'Semester',
                'required' => true,
                'value'    => $this->getSemester(),
                'options'  => array(
                    'options' => array(1 => 'First Semester', 2 => 'Second Semester'),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'reduced_list',
                'label' => 'Send only for subjects with<br>articles last accademic year',
            )
        );

        $this->add(
            array(
                'type'     => 'textarea',
                'name'     => 'message',
                'label'    => 'Message',
                'required' => true,
                'value'    => $this->getMessage(),
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'test_it',
                'label' => 'Send Test to System Administrator',
                'value' => true,
            )
        );

        $this->addSubmit('Send Mail', 'mail');
    }

    /**
     * @param  string $subject
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param  string $message
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param  integer $semester
     * @return self
     */
    public function setSemester($semester)
    {
        $this->semester = $semester;

        return $this;
    }

    /**
     * @return integer
     */
    public function getSemester()
    {
        return $this->semester;
    }
}
