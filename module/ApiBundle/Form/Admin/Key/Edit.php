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

namespace ApiBundle\Form\Admin\Key;

use ApiBundle\Entity\Key,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Text,
    Zend\Form\Element\Submit;

/**
 * Edit Key
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \ApiBundle\Entity\Key $key The key we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Key $key, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'key_edit');
        $this->add($field);

        $this->_populateFromKey($key);
    }

    private function _populateFromKey(Key $key)
    {
        $data = array(
            'host'       => $key->getHost(),
            'check_host' => $key->getCheckHost(),
            'roles'      => $this->_createRolesPopulationArray($key->getRoles()),
        );

        $this->setData($data);
    }

    /**
     * Returns an array that is in the right format to populate the roles field.
     *
     * @param array $roles The key's roles
     * @return array
     */
    private function _createRolesPopulationArray(array $roles)
    {
        $rolesArray = array();
        foreach ($roles as $role) {
            if ($role->getSystem())
                continue;

            $rolesArray[] = $role->getName();
        }
        return $rolesArray;
    }
}
