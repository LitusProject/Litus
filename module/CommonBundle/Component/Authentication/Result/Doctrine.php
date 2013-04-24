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

namespace CommonBundle\Component\Authentication\Result;

use CommonBundle\Entity\Users\Person,
    CommonBundle\Entity\Users\Session;

/**
 * Extending the general authentication result to support Doctrine.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Doctrine extends \CommonBundle\Component\Authentication\Result
{
    /**
     * @var \CommonBundle\Entity\Users\Person The user object given by the DQL query
     */
    private $_personObject = null;

    /**
     * @var \CommonBundle\Entity\Users\Session The session object
     */
    private $_sessionObject = null;

    /**
     * Overwriting the standard constructor to allow for some specific fields.
     *
     * @param int $code The result code
     * @param string $identity The authenticated user's identity
     * @param array $messages The result messages
     * @param \CommonBundle\Entity\Users\Person $personObject The user object given by the DQL query
     * @param \CommonBundle\Entity\Users\Session $sessionObject The session object
     */
    public function __construct($code, $identity, array $messages = array(), Person $personObject = null, Session $sessionObject = null)
    {
        parent::__construct($code, $identity, $messages);

        $this->_personObject = $personObject;
        $this->_sessionObject = $sessionObject;
    }

    /**
     * Return the user object given by the DQL query.
     *
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getPersonObject()
    {
        return $this->_personObject;
    }

    /**
     * Return the session object.
     *
     * @return \CommonBundle\Entity\Users\Session
     */
    public function getSessionObject()
    {
        return $this->_sessionObject;
    }

    /**
     * Setter for the session property.
     *
     * @return \CommonBundle\Component\Authentication\Result\Doctrine
     */
    public function setSessionObject(Session $sessionObject)
    {
        $this->_sessionObject = $sessionObject;
        return $this;
    }
}
