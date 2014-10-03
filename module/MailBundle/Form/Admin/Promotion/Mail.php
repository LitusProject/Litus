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

namespace MailBundle\Form\Admin\Promotion;

use MailBundle\Component\Validator\MultiMail as MultiMailValidator;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $groups = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findAll();

        $groupNames = array();
        foreach ($groups as $group) {
            if (strpos($group->getName(), "Master") === 0) {
                $groupNames[$group->getId()] = $group->getName();
            }
        }

        $this->add(array(
            'type'       => 'select',
            'name'       => 'to',
            'label'      => 'To',
            'required'   => true,
            'attributes' => array(
                'multiple' => true,
            ),
            'options'    => array(
                'options' => $this->_createPromotionsArray(),
            ),
        ));

        if (!empty($groupNames)) {
            $this->add(array(
                'type'       => 'select',
                'name'       => 'groups',
                'label'      => 'Groups',
                'attributes' => array(
                    'multiple' => true,
                    'options'  => $groupNames,
                ),
            ));
        }

        $this->add(array(
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

        $this->add(array(
            'type'       => 'text',
            'name'       => 'bcc',
            'label'      => 'Additional BCC',
            'attributes' => array(
                'style' => 'width: 400px;',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new MultiMailValidator(),
                    ),
                ),
            ),
        ));

        $this->add(array(
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

        $this->add(array(
            'type'       => 'file',
            'name'       => 'file',
            'label'      => 'Attachments',
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

        $this->addSubmit('Send', 'mail');
    }

    private function _createPromotionsArray()
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
}
