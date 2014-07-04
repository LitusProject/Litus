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
namespace LogisticsBundle\Form\Admin\Lease;

use Doctrine\ORM\EntityManager,
    LogisticsBundle\Entity\Lease\Item,
    Zend\Form\Element\Submit;

/**
 * Edits a lease
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param EntityManager   $entityManager
     * @param Item            $lease         The lease item to populate the form with
     * @param null|string|int $name          Optional name for the form
     */
    public function __construct(EntityManager $entityManager, Item $lease, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Edit')
            ->setAttribute('class', 'lease_edit');
        $this->add($field);

        $this->populateFromLease($lease);
    }
}
