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

namespace TicketBundle\Component\Validator;

use Doctrine\ORM\EntityManager,
    TicketBundle\Entity\Event;

/**
 * Check the activity has already a ticket system
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Activity extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var EntityManager
     */
    private $_entityManager;

    /**
     * @var Event|null
     */
    private $_event;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The activity has already a ticket system',
    );

    /**
     * Create a new Article Barcode validator.
     *
     * @param EntityManager $entityManager
     * @param Event|null    $event         The event
     * @param mixed         $opts          The validator's options
     */
    public function __construct(EntityManager $entityManager, Event $event = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_event = $event;
    }

    /**
     * Returns true if these does not exceed max
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $activity = $this->_entityManager
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findOneById($value);

        $event = $this->_entityManager
            ->getRepository('TicketBundle\Entity\Event')
            ->findOneByActivity($activity);

        if (null === $event) {
            return true;
        }

        if ($event->getId() == $this->_event->getId()) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}
