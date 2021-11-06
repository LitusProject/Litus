<?php

namespace QuizBundle\Form\Admin\Quiz;

use RuntimeException;

/**
 * Add a new quiz
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'QuizBundle\Hydrator\Quiz';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'name',
                'label'    => 'Name',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'edit_roles',
                'label'      => 'Edit Roles',
                'required'   => true,
                'attributes' => array(
                    'multiple' => true,
                    'options'  => $this->createEditRolesArray(),
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }

    private function createEditRolesArray()
    {
        $roles = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findBy(array(), array('name' => 'ASC'));

        $rolesArray = array();
        foreach ($roles as $role) {
            if (!$role->getSystem()) {
                $rolesArray[$role->getName()] = $role->getName();
            }
        }

        if (count($rolesArray) == 0) {
            throw new RuntimeException('There needs to be at least one role before you can add a quiz');
        }

        return $rolesArray;
    }
}
