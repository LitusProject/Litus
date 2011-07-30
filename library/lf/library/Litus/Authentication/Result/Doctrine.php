<?php

namespace Litus\Authentication\Result;

use \Litus\Entity\Users\Person;

class Doctrine extends \Litus\Authentication\Result {
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
     * @param \Litus\Entity\Users\Person $personObject The user object given by the DQL query
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
