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
 
namespace CommonBundle\Component\Authentication\Result;

use CommonBundle\Entity\Users\Person;

/**
 * Extending the general authentication result to support Doctrine.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Doctrine extends \CommonBundle\Component\Authentication\Result
{
    /**
     * @var \Litus\Entity\Users\Person The user object given by the DQL query
     */
    private $_personObject = null;

    /**
     * Overwriting the standard constructor to allow for some specific fields.
     *
     * @param int $code The result code
     * @param string $identity The authenticated user's identity
     * @param array $messages The result messages
     * @param \CommonBundle\Entity\Users\Person $personObject The user object given by the DQL query
     */
    public function __construct($code, $identity, array $messages = array(), Person $personObject = null)
    {
        parent::__construct($code, $identity, $messages);
        
        $this->_personObject = $personObject;
    }

    /**
     * Return the user object given by the DQL query.
     *
     * @return \Litus\Entity\Users\Person
     */
    public function getPersonObject()
    {
        return $this->_personObject;
    }
}
