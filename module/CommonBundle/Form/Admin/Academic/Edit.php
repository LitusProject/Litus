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
use CommonBundle\Entity\User\Person\Academic;
use CommonBundle\Entity\User\Status\Organization as OrganizationStatus;
use CommonBundle\Entity\User\Status\University as UniversityStatus;
use LogicException;
use SecretaryBundle\Component\Validator\NoAt as NoAtValidator;

/**
 * Edit Academic
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \CommonBundle\Form\Admin\Person\Edit
{
    protected $hydrator = 'CommonBundle\Hydrator\User\Person\Academic';

    /**
     * @var Academic|null The person we're going to modify
     */
    private $person = null;

    public function init()
    {
        if (null === $this->person) {
            throw new LogicException('Cannot edit null Academic.');
        }

        parent::init();

        $this->add(array(
            'type'       => 'text',
            'name'       => 'birthday',
            'label'      => 'Birthday',
            'attributes' => array(
                'data-help'   => 'The birthday of the user.',
                'placeholder' => 'dd/mm/yyyy',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Date',
                            'options' => array(
                                'format' => 'd/m/Y',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'  => 'common_address_add-primary',
            'name'  => 'primary_address',
            'label' => 'Primary Address&mdash;Student Room or Home',
        ));

        $this->add(array(
            'type'  => 'common_address_add',
            'name'  => 'secondary_address',
            'label' => 'Secondary Address&mdash;Home',
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
                                new BarcodeValidator($this->getEntityManager(), $this->person),
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
                array(
                    'type'       => 'text',
                    'name'       => 'email',
                    'label'      => 'University E-mail',
                    'attributes' => array(
                        'data-help' => 'The e-mail address given to the user by the university.',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                new NoAtValidator(),
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Save', 'academic_edit');

        $this->bind($this->person);
    }

    /**
     * @param  Academic $academic
     * @return self
     */
    public function setAcademic(Academic $academic)
    {
        $this->person = $academic;

        return $this;
    }
}
