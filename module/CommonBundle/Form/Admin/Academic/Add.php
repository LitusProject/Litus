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

use CommonBundle\Component\OldForm\Admin\Element\Checkbox,
    CommonBundle\Component\OldForm\Admin\Element\Collection,
    CommonBundle\Component\OldForm\Admin\Element\Select,
    CommonBundle\Component\OldForm\Admin\Element\Text,
    CommonBundle\Component\Validator\Person\Barcode as BarcodeValidator,
    CommonBundle\Entity\User\Status\Organization as OrganizationStatus,
    CommonBundle\Entity\User\Status\University as UniversityStatus,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Academic
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Form\Admin\Person\Add
{
    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($entityManager, $name);

        $field = new Checkbox('activation_code');
        $field->setLabel('Activation Code')
            ->setAttribute('data-help', 'When checked, an activiation code will be generated and mailed to the user. This code can be used to choose a password, so that it is possible to login without Shibboleth.');
        $this->add($field);

        $collection = new Collection('organization');
        $collection->setLabel('Organization');
        $this->add($collection);

        $field = new Select('organization_status');
        $field->setLabel('Status')
            ->setAttribute(
                'options',
                array_merge(
                    array(
                        '' => ''
                    ),
                    OrganizationStatus::$possibleStatuses
                )
            )
            ->setAttribute('data-help', 'The status of the user in the organization.<br><br><ul>
                <li><b>Member:</b> a member of the organization</li>
                <li><b>Non-Member:</b> the person is not a member of the organization</li>
                <li><b>Honorary Member:</b> the person has earned membership because of his contributions to the organization</li>
                <li><b>Supportive Member:</b> a member, but not a student of the faculty</li>
                <li><b>Praesidium:</b> a member of the board</li>
            </ul>');
        $collection->add($field);

        $field = new Text('barcode');
        $field->setLabel('Barcode')
            ->setAttribute('class', 'disableEnter')
            ->setAttribute('data-help', 'A barcode that can be used to identify the user.');
        $collection->add($field);

        $collection = new Collection('university');
        $collection->setLabel('University');
        $this->add($collection);

        $field = new Select('university_status');
        $field->setLabel('Status')
            ->setAttribute(
                'options',
                array_merge(
                    array(
                        '' => ''
                    ),
                    UniversityStatus::$possibleStatuses
                )
            )
            ->setAttribute('data-help', 'The status of the user in the university.<br><br><ul>
                <li><b>Alumnus:</b> a graduated student</li>
                <li><b>Assistant Professor:</b> an assistant of a professor</li>
                <li><b>Administrative Assistant:</b> an administrative support person</li>
                <li><b>External Student:</b> a student that does not belong to the organization\'s faculty</li>
                <li><b>Professor:</b> a professor</li>
                <li><b>Student:</b> a student</li>
            </ul>');
        $collection->add($field);

        $field = new Text('university_identification');
        $field->setLabel('Identification')
            ->setAttribute('data-help', 'The identification used by the university for the student.');
        $collection->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'academic_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'barcode',
                    'required' => false,
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
                        new BarcodeValidator($this->_entityManager),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'university_identification',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'alnum'
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
