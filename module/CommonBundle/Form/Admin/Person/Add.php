<?php

namespace CommonBundle\Form\Admin\Person;

/**
 * Add Person
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class Add extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'username',
                'label'      => 'Username',
                'required'   => true,
                'attributes' => array(
                    'data-help' => 'A unique identifier for the user (for students, this is automatically set to their university identification).',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Alnum'),
                            array('name' => 'Username'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'first_name',
                'label'      => 'First Name',
                'required'   => true,
                'attributes' => array(
                    'id' => 'first_name',
                ),
                'options' => array(
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
                'type'       => 'text',
                'name'       => 'last_name',
                'label'      => 'Last Name',
                'required'   => true,
                'attributes' => array(
                    'id' => 'last_name',
                ),
                'options' => array(
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
                'type'     => 'text',
                'name'     => 'email',
                'label'    => 'E-mail',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'EmailAddress'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'phone_number',
                'label'      => 'Phone Number',
                'attributes' => array(
                    'placeholder' => '+CCAAANNNNNN',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'PhoneNumber'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'sex',
                'label'      => 'Sex',
                'required'   => true,
                'attributes' => array(
                    'options' => array(
                        'm' => 'M',
                        'f' => 'F',
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'roles',
                'label'      => 'Groups',
                'attributes' => array(
                    'data-help' => 'The roles given to a user control which resources he can access.',
                    'multiple'  => true,
                    'options'   => $this->createRolesArray(),
                ),
            )
        );
    }

    /**
     * Returns an array that has all the roles, so that they are available in the
     * roles multiselect.
     *
     * @return array
     */
    protected function createRolesArray($system = false)
    {
        $roles = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findAll();

        $rolesArray = array();
        foreach ($roles as $role) {
            if ($system !== $role->getSystem()) {
                continue;
            }

            $rolesArray[$role->getName()] = $role->getName();
        }

        return $rolesArray;
    }
}
