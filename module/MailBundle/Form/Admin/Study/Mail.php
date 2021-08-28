<?php

namespace MailBundle\Form\Admin\Study;

use CommonBundle\Entity\General\AcademicYear;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{
    const FILE_SIZE = '50MB';

    /**
     * @var AcademicYear
     */
    private $academicYear;

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'uploadFile');

        $studies = $this->getStudies();
        if (count($studies) != 0) {
            $this->add(
                array(
                    'type'       => 'select',
                    'name'       => 'studies',
                    'label'      => 'Studies',
                    'attributes' => array(
                        'style'    => 'max-width: 400px;',
                        'multiple' => true,
                    ),
                    'options' => array(
                        'options' => $studies,
                    ),
                )
            );
        }

        $groups = $this->getGroups();
        if (count($groups) != 0) {
            $this->add(
                array(
                    'type'       => 'select',
                    'name'       => 'groups',
                    'label'      => 'Groups',
                    'attributes' => array(
                        'multiple' => true,
                    ),
                    'options' => array(
                        'options' => $groups,
                    ),
                )
            );
        }

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'test',
                'label' => 'Test Mail',
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'html',
                'label' => 'HTML Mail',
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'from',
                'label'      => 'From',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 400px;',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'EmailAddress'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'bcc',
                'label'      => 'Additional BCC',
                'attributes' => array(
                    'style' => 'width: 400px;',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'MultiMail'),
                        ),
                    ),
                ),
            )
        );

        $storedMessages = $this->getStoredMessages();
        if (1 < count($storedMessages)) {
            $this->add(
                array(
                    'type'     => 'fieldset',
                    'name'     => 'selected_message',
                    'label'    => 'Select Message',
                    'elements' => array(
                        array(
                            'type'       => 'select',
                            'name'       => 'stored_message',
                            'label'      => 'Stored Message',
                            'attributes' => array(
                                'style' => 'max-width: 100%;',
                            ),
                            'options' => array(
                                'options' => $storedMessages,
                            ),
                        ),
                    ),
                )
            );
        }

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'compose_message',
                'label'    => 'Compose Message',
                'elements' => array(
                    array(
                        'type'       => 'text',
                        'name'       => 'subject',
                        'label'      => 'Subject',
                        'required'   => true,
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
                    ),
                    array(
                        'type'       => 'textarea',
                        'name'       => 'message',
                        'label'      => 'Message',
                        'required'   => true,
                        'attributes' => array(
                            'style' => 'width: 500px; height: 200px;',
                        ),
                        'options' => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'file',
                        'name'       => 'file',
                        'label'      => 'Attachments',
                        'attributes' => array(
                            'multiple'  => true,
                            'data-help' => 'The maximum file size is ' . self::FILE_SIZE . '.',
                        ),
                        'options' => array(
                            'input' => array(
                                'validators' => array(
                                    array(
                                        'name'    => 'FileSize',
                                        'options' => array(
                                            'max' => self::FILE_SIZE,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Send', 'mail', 'Send');
    }

    /**
     * @param  AcademicYear $academicYear
     * @return self
     */
    public function setAcademicYear(AcademicYear $academicYear)
    {
        $this->academicYear = $academicYear;

        return $this;
    }

    private function getStudies()
    {
        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllByAcademicYear($this->academicYear);

        $studyNames = array();
        foreach ($studies as $study) {
            $studyNames[$study->getId()] = 'Phase ' . $study->getPhase() . ' - ' . $study->getTitle();
        }

        return $studyNames;
    }

    private function getGroups()
    {
        $groups = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findAll();

        $groupNames = array();
        foreach ($groups as $group) {
            $groupNames[$group->getId()] = $group->getName();
        }

        return $groupNames;
    }

    private function getStoredMessages()
    {
        $storedMessages = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Message')
            ->findAll();

        $storedMessagesTitles = array(
            '' => '',
        );
        foreach ($storedMessages as $storedMessage) {
            $storedMessagesTitles[$storedMessage->getId()] = '(' . $storedMessage->getCreationTime()->format('d/m/Y') . ') ' . $storedMessage->getSubject();
        }

        return $storedMessagesTitles;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        if ($this->has('selected_message')) {
            $selectedMessageFieldset = $this->get('selected_message');
            if ($selectedMessageFieldset->get('stored_message')->getValue() != '') {
                $specs['compose_message']['subject']['required'] = false;
                $specs['compose_message']['message']['required'] = false;
            }
        }

        return $specs;
    }
}
