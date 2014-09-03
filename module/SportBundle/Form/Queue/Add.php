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

namespace SportBundle\Form\Queue;

/**
 * Add a runner to the queue.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'information',
            'label'      => 'Information',
            'attributes' => array(
                'id' => 'information',
            ),
            'elements'   => array(
                array(
                    'type'       => 'text',
                    'name'       => 'university_identification',
                    'label'      => 'University Identification',
                    'attributes' => array(
                        'id'           => 'university_identification',
                        'autocomplete' => 'off',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'text',
                    'name'       => 'first_name',
                    'label'      => 'First Name',
                    'required'   => true,
                    'attributes' => array(
                        'id'           => 'first_name',
                        'autocomplete' => 'off',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'text',
                    'name'       => 'last_name',
                    'label'      => 'Last Name',
                    'required'   => true,
                    'attributes' => array(
                        'id'           => 'last_name',
                        'autocomplete' => 'off',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'select',
                    'name'       => 'departments',
                    'label'      => 'Departments',
                    'required'   => true,
                    'attributes' => array(
                        'id'      => 'departments',
                        'options' => $this->getDepartments(),
                    ),
                )
            ),
        ));

        $this->addSubmit('Queue', '', 'queue');
        $this->get('queue')->setAttribute('id', 'queue');
    }

    private function getDepartments()
    {
        $departments = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Department')
            ->findAll();

        $array = array('0' => '');
        foreach($departments as $department)
            $array[$department->getId()] = $department->getName();

        return $array;
    }
}
