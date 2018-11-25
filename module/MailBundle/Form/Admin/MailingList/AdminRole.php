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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Form\Admin\MailingList;

use MailBundle\Entity\MailingList;
use RuntimeException;

/**
 * Add Admin Role
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AdminRole extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'MailBundle\Hydrator\MailingList\AdminRoleMap';

    /**
     * @var MailingList
     */
    private $list;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'select',
                'name'     => 'role',
                'label'    => 'Role',
                'required' => true,
                'options'  => array(
                    'options' => $this->createRolesArray(),
                    'input'   => array(
                        'validators' => array(
                            array(
                                'name'    => 'AdminRole',
                                'options' => array(
                                    'list' => $this->getList(),
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
                'name'  => 'edit_admin',
                'label' => 'Can Edit Admins',
            )
        );

        $this->add(
            array(
                'type'       => 'submit',
                'name'       => 'admin_role',
                'value'      => 'Add',
                'attributes' => array(
                    'class' => 'mail_add',
                ),
            )
        );
    }

    private function createRolesArray()
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
            throw new RuntimeException('There needs to be at least one role before you can map a role');
        }

        return $rolesArray;
    }

    /**
     * @param  MailingList $list
     * @return self
     */
    public function setList(MailingList $list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * @return MailingList
     */
    public function getList()
    {
        return $this->list;
    }
}
