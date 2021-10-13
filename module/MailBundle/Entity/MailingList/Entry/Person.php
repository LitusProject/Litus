<?php

namespace MailBundle\Entity\MailingList\Entry;

/**
 * This is an abstract class all person entries should inherit from.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class Person extends \MailBundle\Entity\MailingList\Entry
{
    /**
     * @return string
     */
    abstract public function getFirstName();

    /**
     * @return string
     */
    abstract public function getLastName();
}
