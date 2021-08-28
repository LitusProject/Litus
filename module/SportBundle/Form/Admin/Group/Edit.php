<?php

namespace SportBundle\Form\Admin\Group;

/**
 * Edit a group of friends.
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class Edit extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'person',
                'label'    => 'Runner',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'department',
                'label'      => 'Department',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->getDepartments(),
                ),
            )
        );

        $this->addSubmit('Add Runner', 'product_add');
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
