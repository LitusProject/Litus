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

namespace TicketBundle\Form\Admin\Event;

use Doctrine\ORM\EntityManager,
    TicketBundle\Entity\Event,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit Event
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{

    /**
     * @param \TicketBundle\Entity\Event $event
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Event $event, EntityManager $entityManager, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->populateFromEvent($event);

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'edit');
        $this->add($field);
    }
}