<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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
namespace LogisticsBundle\Form\Admin\Lease;

use Doctrine\ORM\EntityManager,
    LogisticsBundle\Entity\Lease\Item,
    Zend\Form\Element\Submit;

/**
 * Edits a lease
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class Edit extends Add {

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \LogisticsBundle\Entity\Lease\Item $lease The lease item to populate the form with
     * @param null|string|int $name Optional name for the form
     */
    public function __construct(EntityManager $entityManager, Item $lease, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Edit')
            ->setAttribute('class', 'edit');
        $this->add($field);

        $this->populateFromLease($lease);
    }
}
