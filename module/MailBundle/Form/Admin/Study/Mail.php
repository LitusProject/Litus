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

use CommonBundle\Entity\General\AcademicYear,
    MailBundle\Component\Validator\MultiMail as MultiMailValidator;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var AcademicYear
     */
    private $_academicYear;

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'uploadFile');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('accept-charset', 'utf-8');

        $studies = $this->_getStudies();
        $studyNames = array();
        foreach($studies as $study)
            $studyNames[$study->getId()] = 'Phase ' . $study->getPhase() . ' - ' . $study->getFullTitle();

        $groups = $this->_getGroups();
        $groupNames = array();
        foreach($groups as $group)
            $groupNames[$group->getId()] = $group->getName();

        $storedMessages = $this->_getStoredMessages();
        $storedMessagesTitles = array(
            '' => ''
        );
        foreach ($storedMessages as $storedMessage)
            $storedMessagesTitles[$storedMessage->getId()] = '(' . $storedMessage->getCreationTime()->format('d/m/Y') . ') ' . $storedMessage->getSubject();

        if (0 != count($studyNames)) {
            $this->add(array(
                'type'       => 'select',
                'name'       => 'studies',
                'label'      => 'Studies',
                'attributes' => array(
                    'style' => 'max-width: 400px;',
                    'multiple' => true,
                ),
                'options'    => array(
                    'options' => $studyNames,
                ),
            ));
        }

        if (0 != count($groupNames)) {
            $this->add(array(
                'type'       => 'select',
                'name'       => 'groups',
                'label'      => 'Groups',
                'attributes' => array(
                    'multiple' => true,
                ),
                'options'    => array(
                    'options' => $groupNames,
                ),
            ));
        }

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'test',
            'label'      => 'Test Mail',
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'html',
            'label'      => 'HTML Mail',
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'from',
            'label'      => 'From',
            'required'   => true,
            'attributes' => array(
                'style' => 'width: 400px;',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'emailAddress'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'bcc',
            'label'      => 'Additional BCC',
            'attributes' => array(
                'style' => 'width: 400px;',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new MultiMailValidator(),
                    ),
                ),
            ),
        ));

        $selectMessage = null;
        if (0 != count($storedMessages)) {
            $selectMessage = $this->addFieldset('Select Message', 'select_message');

            $selectMessage->add(array(
                'type'       => 'select',
                'name'       => 'stored_message',
                'label'      => 'Stored Message',
                'attributes' => array(
                    'style' => 'max-width: 100%;',
                ),
                'options'    => array(
                    'options' => $storedMessagesTitles,
                ),
            ));
        }

        $fieldset = $this->addFieldset('Compose Message', 'compose_message');

        $fieldset->add(array(
            'type'       => 'text',
            'name'       => 'subject',
            'label'      => 'Subject',
            'required'   => true,
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

        $fieldset->add(array(
            'type'       => 'textarea',
            'name'       => 'message',
            'label'      => 'Message',
            'required'   => true,
            'attributes' => array(
                'style' => 'width: 500px; height: 200px;',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $fieldset->add(array(
            'type'       => 'file',
            'name'       => 'file',
            'label'      => 'Attachments',
            'required'   => true,
            'attributes' => array(
                'multiple' => true,
            ),
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name' => 'filefilessize',
                            'options' => array(
                                'max' => '50MB',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'submit',
            'name'       => 'send',
            'value'      => 'Send',
            'attributes' => array(
                'class' => 'mail',
            ),
        ));
    }

    /**
     * @param  AcademicYear $academicYear
     * @return self
     */
    public function setAcademicYear(AcademicYear $academicYear)
    {
        $this->_academicYear = $academicYear;

        return $this;
    }

    private function _getStudies()
    {
        return $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllParentsByAcademicYear($this->_academicYear);
    }

    private function _getGroups()
    {
        return $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findAll();
    }

    private function _getStoredMessages()
    {
        return $this->getDocumentManager()
            ->getRepository('MailBundle\Document\Message')
            ->findAll(array(), array('creationTime' => 'DESC'));
    }
}
