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

namespace CommonBundle\Form\Admin\Academic;

use CommonBundle\Component\Validator\Person\Barcode as BarcodeValidator;
use CommonBundle\Entity\User\Status\Organization as OrganizationStatus;
use CommonBundle\Entity\User\Status\University as UniversityStatus;

/**
 * Add Academic
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Form\Admin\Person\Add
{
    protected $hydrator = 'CommonBundle\Hydrator\User\Person\Academic';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'activation_code',
            'label'      => 'Activation Code',
            'required'   => true,
            'attributes' => array(
                'data-help' => 'When checked, an activiation code will be generated and mailed to the user. This code can be used to choose a password, so that it is possible to login without Shibboleth.',
            ),
        ));

        $this->add(array(
            'type'     => 'fieldset',
            'name'     => 'organization',
            'label'    => 'Organization',
            'elements' => array(
                array(
                    'type'       => 'select',
                    'name'       => 'status',
                    'label'      => 'Status',
                    'attributes' => array(
                        'data-help' => 'The status of the user in the organization.<br><br><ul>
                                <li><b>Member:</b> a member of the organization</li>
                                <li><b>Non-Member:</b> the person is not a member of the organization</li>
                                <li><b>Honorary Member:</b> the person has earned membership because of his contributions to the organization</li>
                                <li><b>Supportive Member:</b> a member, but not a student of the faculty</li>
                                <li><b>Praesidium:</b> a member of the board</li>
                            </ul>',
                        'options'   => array_merge(
                            array(
                                '' => '',
                            ),
                            OrganizationStatus::$possibleStatuses
                        ),
                    ),
                ),
                array(
                    'type'       => 'text',
                    'name'       => 'barcode',
                    'label'      => 'Barcode',
                    'attributes' => array(
                        'class'     => 'disableEnter',
                        'data-help' => 'A barcode that can be used to identify the user.',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name' => 'barcode',
                                    'options' => array(
                                        'adapter'     => 'Ean12',
                                        'useChecksum' => false,
                                    ),
                                ),
                                new BarcodeValidator($this->getEntityManager()),
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'fieldset',
            'name'     => 'university',
            'label'    => 'University',
            'elements' => array(
                array(
                    'type'       => 'select',
                    'name'       => 'status',
                    'label'      => 'Status',
                    'attributes' => array(
                        'data-help' => 'The status of the user in the university.<br><br><ul>
                                <li><b>Alumnus:</b> a graduated student</li>
                                <li><b>Assistant Professor:</b> an assistant of a professor</li>
                                <li><b>Administrative Assistant:</b> an administrative support person</li>
                                <li><b>External Student:</b> a student that does not belong to the organization\'s faculty</li>
                                <li><b>Professor:</b> a professor</li>
                                <li><b>Student:</b> a student</li>
                            </ul>',
                        'options'   => array_merge(
                            array(
                                '' => '',
                            ),
                            UniversityStatus::$possibleStatuses
                        ),
                    ),
                ),
                array(
                    'type'       => 'text',
                    'name'       => 'identification',
                    'label'      => 'Identification',
                    'attributes' => array(
                        'data-help' => 'The identification used by the university for the student.',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name' => 'alnum',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'academic_add');
    }
}
