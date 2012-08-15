<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CalendarBundle\Form\Admin\Event;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\QueryBuilder,
    CalendarBundle\Entity\Nodes\Event,
    Zend\Form\Element\Submit;

/**
 * Edit an event.
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, Event $event, $opts = null)
    {
        parent::__construct($entityManager, $opts);

        $this->removeElement('submit');

        $this->event = $event;

        $field = new Submit('submit');
        $field->setLabel('Save')
            ->setAttrib('class', 'calendar_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

        $this->populateFromEvent($event);
    }
}
