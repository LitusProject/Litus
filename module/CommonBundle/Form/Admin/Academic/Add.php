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

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\Person\Barcode as BarcodeValidator,
    CommonBundle\Entity\User\Status\Organization as OrganizationStatus,
    CommonBundle\Entity\User\Status\University as UniversityStatus,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
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
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($entityManager, $name);

        $field = new Checkbox('activation_code');
        $field->setLabel('Activation Code')
            ->setAttribute('data-help', 'With this activation code the user as the ability to login without shibboleth. A mail will be send with an activation code.');
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
            ->setAttribute('data-help', 'The user status in the organiation: <ul>
                <li><b>Member:</b> a regular member</li>
                <li><b>Non-member:</b> a regular non member</li>
                <li><b>Honorary member:</b> a member due to special earnings</li>
                <li><b>Supportive member:</b> a member, but not a student</li>
                <li><b>Praesidium:</b> a member of the board</li>
            </ul>');
        $collection->add($field);

        $field = new Text('barcode');
        $field->setLabel('Barcode')
            ->setAttribute('class', 'disableEnter')
            ->setAttribute('data-help', 'The barcode used to identify the user in this organization.');
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
            ->setAttribute('data-help', 'The user status in the university: <ul>
                <li><b>Alumnus:</b> Old student</li>
                <li><b>Assistant Professor:</b> Assistant of a professor</li>
                <li><b>Administrative Assistant:</b> Assistant</li>
                <li><b>External Student:</b> A external student currently studying at this university</li>
                <li><b>Professor:</b> A professor</li>
                <li><b>Student:</b> A student</li>
            </ul>');
        $collection->add($field);

        $field = new Text('university_identification');
        $field->setLabel('Identification')
            ->setAttribute('data-help', 'The unique user identification of the university.');
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
