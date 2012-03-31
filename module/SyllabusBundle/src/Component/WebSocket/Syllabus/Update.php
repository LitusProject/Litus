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
 
namespace SyllabusBundle\Component\WebSocket\Syllabus;

use CommonBundle\Component\WebSocket\User,
	Doctrine\ORM\EntityManager,
    SyllabusBundle\Component\XMLParser\Study as StudyParser;

/**
 * This is the server to handle all requests by the websocket protocol for the Queue.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Update extends \CommonBundle\Component\WebSocket\Server
{
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $_entityManager;
	
	/**
	 * @var string
	 */
	private $_status = 'done';
	
	/**
	 * @param Doctrine\ORM\EntityManager $entityManager
	 * @param string $address The url for the websocket master socket
	 * @param integer $port The port to listen on
	 */
	public function __construct(EntityManager $entityManager)
	{
	    $address = $entityManager
    		->getRepository('CommonBundle\Entity\General\Config')
    		->getConfigValue('syllabus.update_socket_host');
    	$port = $entityManager
    		->getRepository('CommonBundle\Entity\General\Config')
    		->getConfigValue('syllabus.update_socket_port');
    
    	parent::__construct($address, $port);
	    	
	    $this->_entityManager = $entityManager;
	}

	/**
	 * Parse received text
	 *
	 * @param \CommonBundle\Component\WebSockets\Sale\User $user
	 * @param string $data
	 */
	protected function gotText(User $user, $data)
	{
		$this->_entityManager->clear();

		if (strpos($data, 'update') === 0 && 'done' == $this->_status) {
		    $this->_status = 'updating';
			new StudyParser($this->_entityManager, 'http://litus/admin/syllabus/update/xml', array($this, 'sendTextToAll'));
			$this->_status = 'done';
		}
	}
}