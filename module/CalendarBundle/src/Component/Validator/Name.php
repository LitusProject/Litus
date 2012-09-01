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

namespace CalendarBundle\Component\Validator;

use CommonBundle\Component\Util\Url,
    CommonBundle\Entity\General\Language,
    Doctrine\ORM\EntityManager,
    CalendarBundle\Entity\Nodes\Event;

/**
 * Matches the given event title against the database to check whether it is
 * unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Name extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \CalendarBundle\Entity\Nodes\Event The event exluded from this check
     */
    private $_event;

    /**
     * @var \CommonBundle\Entity\General\Language
     */
    private $_language;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This event title already exists'
    );

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\General\Language $language
     * @param \CalendarBundle\Entity\Nodes\Event The event exluded from this check
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, Language $language, Event $event = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_language = $language;
        $this->_event = $event;
    }

    /**
     * Returns true if no matching record is found in the database.
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $date = \DateTime::createFromFormat('d#m#Y H#i', $context['start_date']);

        if ($date) {
            $title = $date->format('Ymd') . '_' . Url::createSlug($value);

            $event = $this->_entityManager
                ->getRepository('CalendarBundle\Entity\Nodes\Event')
                ->findOneByName($title);

            if (null === $event || ($this->_event && $event->getEvent() == $this->_event))
                return true;

            $this->error(self::NOT_VALID);
        }

        return false;
    }
}
