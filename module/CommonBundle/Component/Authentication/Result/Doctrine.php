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

namespace CommonBundle\Component\Authentication\Result;

use CommonBundle\Entity\User\Person,
    CommonBundle\Entity\User\Session,
    RuntimeException;

/**
 * Extending the general authentication result to support Doctrine.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Doctrine extends \CommonBundle\Component\Authentication\Result
{
    /**
     * @var Person The user object given by the DQL query
     */
    private $personObject = null;

    /**
     * @var Session The session object
     */
    private $sessionObject = null;

    /**
     * Overwriting the standard constructor to allow for some specific fields.
     *
     * @param int          $code          The result code
     * @param string       $identity      The authenticated user's identity
     * @param array        $messages      The result messages
     * @param Person|null  $personObject  The user object given by the DQL query
     * @param Session|null $sessionObject The session object
     */
    public function __construct($code, $identity, array $messages = array(), Person $personObject = null, Session $sessionObject = null)
    {
        parent::__construct($code, $identity, $messages);

        $this->personObject = $personObject;
        $this->sessionObject = $sessionObject;
    }

    /**
     * @return boolean
     */
    public function hasPersonObject()
    {
        return (null !== $this->personObject);
    }

    /**
     * Return the user object given by the DQL query.
     *
     * @return Person
     */
    public function getPersonObject()
    {
        if (null === $this->personObject) {
            throw new RuntimeException('No user was authenticated');
        }

        return $this->personObject;
    }

    /**
     * Return the session object.
     *
     * @return Session
     */
    public function getSessionObject()
    {
        return $this->sessionObject;
    }

    /**
     * Setter for the session property.
     *
     * @return self
     */
    public function setSessionObject(Session $sessionObject)
    {
        $this->sessionObject = $sessionObject;

        return $this;
    }
}
