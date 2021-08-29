<?php

namespace ApiBundle\Form\Admin\Key;

use Laminas\Validator\Hostname as HostnameValidator;
use RuntimeException;

/**
 * Add Key
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'ApiBundle\Hydrator\Key';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'host',
                'label'      => 'Host',
                'required'   => true,
                'attributes' => array(
                    'data-help' => 'The host from which the API can be accessed with the key.',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'Hostname',
                                'options' => array(
                                    'allow' => HostnameValidator::ALLOW_ALL,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'check_host',
                'label' => 'Check Host',
                'value' => true,
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'roles',
                'label'      => 'Groups',
                'attributes' => array(
                    'multiple' => true,
                    'options'  => $this->createRolesArray(),
                ),
            )
        );

        $this->addSubmit('Add', 'key_add');
    }

    /**
     * Returns an array that has all the roles, so that they are available in the
     * roles multiselect.
     *
     * @return array
     */
    protected function createRolesArray()
    {
        $roles = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findBy(array(), array('name' => 'ASC'));

        $rolesArray = array();
        foreach ($roles as $role) {
            if ($role->getSystem()) {
                continue;
            }

            $rolesArray[$role->getName()] = $role->getName();
        }

        if (count($rolesArray) == 0) {
            throw new RuntimeException('There needs to be at least one role before you can add an API key');
        }

        return $rolesArray;
    }
}
