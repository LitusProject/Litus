<?php

namespace CommonBundle\Form\Admin\Role;

use CommonBundle\Entity\Acl\Role;

/**
 * Add Role
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CommonBundle\Hydrator\Acl\Role';

    /**
     * @var Role The role to edit, if any.
     */
    protected $role;

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
                        'validators' => array(
                            array('name' => 'Role'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'parents',
                'label'      => 'Parents',
                'attributes' => array(
                    'multiple' => true,
                    'options'  => $this->createParentsArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'actions',
                'label'      => 'Allowed Actions',
                'attributes' => array(
                    'id'       => 'actions',
                    'multiple' => true,
                    'options'  => $this->createActionsArray(),
                    'style'    => 'height: 300px;',
                ),
            )
        );

        $this->addSubmit('Add', 'role_add');

        if ($this->role !== null) {
            $this->bind($this->role);
        }
    }

    /**
     * @param  Role $role The role to edit, if any
     * @return self
     */
    public function setRole(Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Returns an array that has all the roles, so that they are available in the
     * parents multiselect.
     *
     * @return array
     */
    protected function createParentsArray()
    {
        $roles = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findBy(array(), array('name' => 'ASC'));

        $exclude = $this->role === null ? '' : $this->role->getName();

        $parents = array();
        foreach ($roles as $role) {
            if ($role->getName() != $exclude) {
                $parents[$role->getName()] = $role->getName();
            }
        }

        return $parents;
    }

    /**
     * Returns an array that has all the actions that are currently in the database
     * so that we can assign some to this role.
     *
     * @return array
     */
    private function createActionsArray()
    {
        $resources = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Resource')
            ->findByParent(null);

        $actions = array();
        foreach ($resources as $resource) {
            $resourceChildren = $resource->getChildren($this->getEntityManager());
            foreach ($resourceChildren as $resourceChild) {
                $childActions = $resourceChild->getActions($this->getEntityManager());
                $actions[$resourceChild->getName()] = array(
                    'label'   => $resourceChild->getName(),
                    'options' => array(),
                );
                foreach ($childActions as $childAction) {
                    $actions[$resourceChild->getName()]['options'][$childAction->getId()] = $childAction->getName();
                }

                asort($actions[$resourceChild->getName()]['options']);
            }
        }

        ksort($actions);

        return $actions;
    }
}
