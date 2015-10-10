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

use SportBundle\Entity\Group;

/**
 * Add a group of friends.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'SportBundle\Hydrator\Group';

    protected $happy_hours_1, $happy_hours_2;

    protected function getHappyHours1()
    {
        return $this->happy_hours_1;
    }

    protected function getHappyHours2()
    {
        return $this->happy_hours_2;
    }

    public function setHappyHours1($happy_hours_1)
    {
        $this->happy_hours_1 = $happy_hours_1;
    }

    public function setHappyHours2($happy_hours_2)
    {
        $this->happy_hours_2 = $happy_hours_2;
    }

    public function init()
    {
        parent::init();

        $this->add(array(
            'type' => 'fieldset',
            'name' => 'group_information',
            'label' => 'Group Information',
            'attributes' => array(
                'id' => 'group_information',
            ),
            'elements' => array(
                array(
                    'type' => 'text',
                    'name' => 'name',
                    'label' => 'Group Name',
                    'required' => true,
                    'options' => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type' => 'select',
                    'name' => 'happy_hour_one',
                    'label' => 'First Happy Hour',
                    'attributes' => array(
                        'options' => $this->getHappyHours1(),
                    ),
                ),
                array(
                    'type' => 'select',
                    'name' => 'happy_hour_two',
                    'label' => 'Second Happy Hour',
                    'attributes' => array(
                        'options' => $this->getHappyHours2(),
                    ),
                ),
            ),
        ));

        foreach (Group::$allMembers as $i => $memberNb) {
            $this->generateMemberForm($memberNb, ($i < 2));
        }

        $this->addSubmit('Submit');
    }

    /**
	 * @param string $memberNb
	 */
    private function generateMemberForm($memberNb, $required = false)
    {
        $this->add(array(
            'type' => 'fieldset',
            'name' => 'user_' . $memberNb,
            'label' => 'Runner ' . ucfirst($memberNb),
            'attributes' => array(
                'id' => 'user_' . $memberNb,
            ),
            'elements' => array(
                array(
                    'type' => 'text',
                    'name' => 'university_identification',
                    'label' => 'University Identification',
                    'attributes' => array(
                        'id' => 'university_identification_' . $memberNb,
                    ),
                    'required' => $required,
                    'options' => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name' => 'university_identification',
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'type' => 'text',
                    'name' => 'first_name',
                    'label' => 'First Name',
                    'required' => $required,
                    'options' => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type' => 'text',
                    'name' => 'last_name',
                    'label' => 'Last Name',
                    'required' => $required,
                    'options' => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type' => 'select',
                    'name' => 'department',
                    'label' => 'Department',
                    'requied' => $required,
                    'attributes' => array(
                        'options' => $this->getDepartments(),
                    ),
                ),
            ),
        ));
    }

    private function getDepartments()
    {
        $departments = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Department')
            ->findAll();

        $array = array('0' => '');
        foreach ($departments as $department) {
            $array[$department->getId()] = $department->getName();
        }

        return $array;
    }
}
