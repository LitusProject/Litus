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

/**
 * Add a group of friends.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var string[] An array containing all members that should be created
     */
    private $allMembers = array();

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'group_information',
            'label'      => 'Group Information',
            'attributes' => array(
                'id' => 'group_information',
            ),
            'elements'   => array(
                array(
                    'type'     => 'text',
                    'name'     => 'name',
                    'label'    => 'Group Name',
                    'required' => true,
                    'options'  => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'select',
                    'name'       => 'happy_hour_one',
                    'label'      => 'First Happy Hour',
                    'attributes' => array(
                        'options' => $this->generateHappyHours(20),
                    ),
                ),
                array(
                    'type'       => 'select',
                    'name'       => 'happy_hour_two',
                    'label'      => 'Second Happy Hour',
                    'attributes' => array(
                        'options' => $this->generateHappyHours(8),
                    ),
                ),
            ),
        ));

        foreach ($allMembers as $i => $memberNb) {
            $this->generateMemberForm($memberNb, ($i < 2));
        }

        $this->addSubmit('Submit');
    }

    /**
     * @param  string[] $allMembers
     * @return self
     */
    public function setAllMembers(array $allMembers)
    {
        $this->allMembers = $allMembers;

        return $this;
    }

    /**
     * @param integer $startTime
     */
    private function generateHappyHours($startTime)
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

        $groups = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Group')
            ->findLast();

        return $this->_cleanHappyHoursArray($optionsArray, $groups);
    }

    private function cleanHappyHoursArray(array $optionsArray, array $groups)
    {
        $returnArray = $optionsArray;
        foreach ($groups as $group) {
            $happyHours = $group->getHappyHours();

            if (isset($returnArray[$happyHours[0]]))
                unset($returnArray[$happyHours[0]]);

            if (isset($returnArray[$happyHours[1]]))
                unset($returnArray[$happyHours[1]]);
        }

        return $returnArray;
    }

    /**
     * @param string $memberNb
     */
    private function generateMemberForm($memberNb, $required = false)
    {
        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'user_' . $memberNb,
            'label'      => 'Runner ' . ucfirst($memberNb),
            'attributes' => array(
                'id' => 'user_' . $memberNb,
            ),
            'elements'   => array(
                array(
                    'type'    => 'text',
                    'name'    => 'university_identification',
                    'label'   => 'University Identification',
                    'options' => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'     => 'text',
                    'name'     => 'first_name',
                    'label'    => 'First Name',
                    'required' => $required,
                    'options'  => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'     => 'text',
                    'name'     => 'last_name',
                    'label'    => 'Last Name',
                    'required' => $required,
                    'options'  => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'select',
                    'name'       => 'department',
                    'label'      => 'Department',
                    'requied'    => $required,
                    'attributes' => array(
                        'options' => $this->getDepartments(),
                    ),
                )
            ),
        ));
    }

    private function getDepartments()
    {
        $departments = $this->_entityManager
            ->getRepository('SportBundle\Entity\Department')
            ->findAll();

        $array = array('0' => '');
        foreach($departments as $department)
            $array[$department->getId()] = $department->getName();

        return $array;
    }
}
