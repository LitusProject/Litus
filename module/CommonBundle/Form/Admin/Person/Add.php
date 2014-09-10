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

namespace CommonBundle\Form\Admin\Person;

use CommonBundle\Component\Validator\PhoneNumber as PhoneNumberValidator;
use CommonBundle\Component\Validator\Username as UsernameValidator;

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

        $this->add(array(
            'type'       => 'text',
            'name'       => 'username',
            'label'      => 'Username',
            'required'   => true,
            'attributes' => array(
                'data-help' => 'A unique identifier for the user (for students, this is automatically set to their university identification).',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'alnum',
                        ),
                        new UsernameValidator($this->getEntityManager()),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'first_name',
            'label'    => 'First Name',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'last_name',
            'label'    => 'Last Name',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'email',
            'label'    => 'E-mail',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'EmailAddress',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'phone_number',
            'label'      => 'Phone Number',
            'attributes' => array(
                'placeholder' => '+CCAAANNNNNN',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new PhoneNumberValidator(),
                    ),
                ),
            ),
        ));

        $this->add(array(
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
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'roles',
            'label'      => 'Groups',
            'attributes' => array(
                'data-help' => 'The roles given to a user control which resources he can access.',
                'multiple'  => true,
                'options'   => $this->createRolesArray(),
            ),
        ));
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
            if ($system !== $role->getSystem())
                continue;

            $rolesArray[$role->getName()] = $role->getName();
        }

        return $rolesArray;
    }
}
