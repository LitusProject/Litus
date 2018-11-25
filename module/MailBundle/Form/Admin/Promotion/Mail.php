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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Form\Admin\Promotion;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{
    const FILE_SIZE = '50MB';

    public function init()
    {
        parent::init();

        $groups = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findAll();

        $groupNames = array();
        foreach ($groups as $group) {
            if (strpos($group->getName(), 'Master') === 0) {
                $groupNames[$group->getId()] = $group->getName();
            }
        }

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'to',
                'label'      => 'To',
                'required'   => true,
                'attributes' => array(
                    'multiple' => true,
                ),
                'options' => array(
                    'options' => $this->createPromotionsArray(),
                ),
            )
        );

        if (count($groupNames) > 0) {
            $this->add(
                array(
                    'type'       => 'select',
                    'name'       => 'groups',
                    'label'      => 'Groups',
                    'attributes' => array(
                        'multiple' => true,
                        'options'  => $groupNames,
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
        if (1 <= count($storedMessages)) {
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

    private function createPromotionsArray()
    {
        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $promotionsArray = array();
        foreach ($academicYears as $academicYear) {
            $promotionsArray[$academicYear->getId()] = $academicYear->getCode();
        }

        return $promotionsArray;
    }

    private function getStoredMessages()
    {
        $storedMessages = $this->getDocumentManager()
            ->getRepository('MailBundle\Document\Message')
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
