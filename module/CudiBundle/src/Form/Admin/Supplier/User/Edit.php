<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\Supplier\User;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Entity\Users\Person,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Edit a user's data.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CommonBundle\Form\Admin\Person\Edit
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\Users\Person $person The person we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Person $person, $name = null)
    {
        parent::__construct($entityManager, $person, $name);

        $this->remove('system_roles');
        $this->remove('roles');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'user_edit');
        $this->add($field);
    }
}
