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

namespace TicketBundle\Component\Validator;

use CommonBundle\Entity\User\Person,
    Doctrine\ORM\EntityManager,
    TicketBundle\Entity\Event;

/**
 * Check whether number of member + number of non member does not exceed max
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class NumberTickets extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @var \TicketBundle\Entity\Event
     */
    private $_event;

    /**
     * @var \CommonBundle\Entity\User\Person
     */
    private $_person;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The number of tickets exceeds the maximum'
    );

    /**
     * Create a new Article Barcode validator.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \TicketBundle\Entity\Event $event The event
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, Event $event, Person $person = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_event = $event;
        $this->_person = $person;
    }


    /**
     * Returns true if these does not exceed max
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $number = 0;
        if (count($this->_event->getOptions()) == 0) {
            $number += $context['number_member'];
            if (!$this->_event->isOnlyMembers()) {
                $number += $context['number_non_member'];
            }
        } else {
            foreach($this->_event->getOptions() as $option) {
                $number += $context['option_' . $option->getId() . '_number_member'];
                if (!$this->_event->isOnlyMembers()) {
                    $number += $context['option_' . $option->getId() . '_number_non_member'];
                }
            }
        }

        if ($this->_person == null) {
            $person = $this->_entityManager
                ->getRepository('CommonBundle\Entity\User\Person')
                ->findOneById($context['person_id']);
        } else {
            $person = $this->_person;
        }

        if (null == $person ) {
            $this->error(self::NOT_VALID);
            return false;
        }

        $tickets = $this->_entityManager
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findAllByEventAndPerson($this->_event, $person);

        if ($number + sizeof($tickets) > $this->_event->getLimitPerPerson() && $this->_event->getLimitPerPerson() != 0) {
            $this->error(self::NOT_VALID);
            return false;
        }

        return true;
    }
}
