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
    private $_subject;

    /**
     * @var string
     */
    private $_message;

    /**
     * @var string
     */
    private $_semester;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'text',
            'name'       => 'subject',
            'label'      => 'Subject',
            'required'   => true,
            'value'      => $this->getSubject(),
            'attributes' => array(
                'style' => 'width: 400px;',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'semester',
            'label'      => 'Semester',
            'required'   => true,
            'value'      => $this->getSemester(),
            'options'    => array(
                'options' => array(1 => 'First Semester', 2 => 'Second Semester'),
            ),
        ));

        $this->add(array(
            'type'       => 'textarea',
            'name'       => 'message',
            'label'      => 'Message',
            'required'   => true,
            'value'      => $this->getMessage(),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'test_it',
            'label'      => 'Send Test to System Administrator',
            'value'   => true,
        ));

        $this->addSubmit('Send Mail', 'mail');
    }

    /**
     * @param  string $subject
     * @return self
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * @param  string $message
     * @return self
     */
    public function setMessage($message)
    {
        $this->_message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @param  string $academicYear
     * @return self
     */
    public function setSemester($semester)
    {
        $this->_semester = $semester;
        return $this;
    }

    /**
     * @return string
     */
    public function getSemester()
    {
        return $this->_semester;
    }
}
