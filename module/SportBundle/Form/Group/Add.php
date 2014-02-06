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

namespace SportBundle\Form\Group;

use CommonBundle\Component\Form\Bootstrap\Element\Collection,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add a group of friends.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var array An array containing all members that should be created
     */
    private $_allMembers = array();

    /**
     * @param array $allMembers
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, array $allMembers, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_allMembers = $allMembers;

        $group = new Collection('group_information');
        $group->setLabel('Group Information')
            ->setAttribute('id', 'group_information');
        $this->add($group);

        $field = new Text('group_name');
        $field->setLabel('Group Name')
            ->setRequired();
        $group->add($field);

        $field = new Select('happy_hour_one');
        $field->setLabel('First Happy Hour')
            ->setAttribute('options', $this->_generateHappyHours(20));
        $group->add($field);

        $field = new Select('happy_hour_two');
        $field->setLabel('Second Happy Hour')
            ->setAttribute('options', $this->_generateHappyHours(8));
        $group->add($field);

        foreach ($allMembers as $i => $memberNb) {
            $this->_generateMemberForm($memberNb, ($i < 2));
        }

        $field = new Submit('submit');
        $field->setValue('Submit');
        $this->add($field);
    }

    private function _generateHappyHours($startTime)
    {
        $optionsArray = array();
        for ($i = 0; $i < 6; $i++) {
            $startInterval = ($startTime + 2 * $i) % 24;
            if ($startInterval < 10)
                $startInterval = 0 . $startInterval;

            $endInterval = ($startTime + 2 * ($i + 1)) % 24;
            if ($endInterval < 10)
                $endInterval = 0 . $endInterval;

            $optionKey = $startInterval . $endInterval;
            $optionValue = $startInterval . ':00 - ' . $endInterval . ':00';

            $optionsArray[$optionKey] = $optionValue;
        }

        $groups = $this->_entityManager
            ->getRepository('SportBundle\Entity\Group')
            ->findAll();

        $returnArray = $this->_cleanHappyHoursArray($optionsArray, $groups);

        if (0 == count($returnArray)) {
            $returnArray = $optionsArray;
            if (0 != count($groups))
                $returnArray = $this->_cleanHappyHoursArray($optionsArray, $groups);
        }

        return $returnArray;
    }

    private function _cleanHappyHoursArray($optionsArray, &$groups)
    {
        $returnArray = $optionsArray;
        for($i = 0; $i < (count($groups) % 6); $i++) {
            $happyHours = $groups[$i]->getHappyHours();

            if (isset($returnArray[$happyHours[0]]))
                unset($returnArray[$happyHours[0]]);

            if (isset($returnArray[$happyHours[1]]))
                unset($returnArray[$happyHours[1]]);

            unset($groups[$i]);
        }

        $newGroups = array();
        foreach($groups as $groupNb => $group)
            $newGroups[$groupNb - 6] = $group;
        $groups = $newGroups;

        return $returnArray;
    }

    private function _generateMemberForm($memberNb, $required = false)
    {
        $user = new Collection('user_' . $memberNb);
        $user->setLabel('Runner ' . ucfirst($memberNb))
            ->setAttribute('id', 'user_' . $memberNb);
        $this->add($user);

        $field = new Text('university_identification_' . $memberNb);
        $field->setLabel('University Identification');
        $user->add($field);

        $field = new Text('first_name_' . $memberNb);
        $field->setLabel('First Name')
            ->setRequired($required);
        $user->add($field);

        $field = new Text('last_name_' . $memberNb);
        $field->setLabel('Last Name')
            ->setRequired($required);
        $user->add($field);

        $field = new Select('department_' . $memberNb);
        $field->setLabel('Department')
            ->setRequired($required)
            ->setAttribute('options', $this->_getDepartments());
        $user->add($field);
    }

    private function _getDepartments()
    {
        $departments = $this->_entityManager
            ->getRepository('SportBundle\Entity\Department')
            ->findAll();

        $array = array('0' => '');
        foreach($departments as $department)
            $array[$department->getId()] = $department->getName();

        return $array;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'group_name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'happy_hour_one',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'happy_hour_two',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        foreach ($this->_allMembers as $i => $memberNb) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'university_identification_' . $memberNb,
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'first_name_' . $memberNb,
                        'required' => ($i < 2),
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'last_name_' . $memberNb,
                        'required' => ($i < 2),
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );
        }

        return $inputFilter;
    }
}
