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
 
namespace CudiBundle\Component\WebSocket\Sale;

use CommonBundle\Component\WebSocket\User,
	Doctrine\ORM\EntityManager;

/**
 * This is the server to handle all requests by the websocket protocol for the Queue.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Queue extends \CommonBundle\Component\WebSocket\Server
{
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $_entityManager;
	
	/**
	 * @param Doctrine\ORM\EntityManager $entityManager
	 * @param string $address The url for the websocket master socket
	 * @param integer $port The port to listen on
	 */
	public function __construct(EntityManager $entityManager)
	{
		$address = $entityManager
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.queue_socket_host');
		$port = $entityManager
			->getRepository('CommonBundle\Entity\General\Config')
			->getConfigValue('cudi.queue_socket_port');
	
		parent::__construct($address, $port);
		$this->_entityManager = $entityManager;
	}

	/**
	 * Parse received text
	 *
	 * @param \CommonBundle\Component\WebSockets\User $user
	 * @param string $data
	 */
	protected function gotText(User $user, $data)
	{
		if ($data == 'queueUpdated')
			$this->sendTextToAll($this->getJsonQueue());
	}
	
	/**
	 * Do action when a new user has connected to this socket
	 *
	 * @param \CommonBundle\Component\WebSockets\User $user
	 */
	protected function onConnect(User $user)
	{
		$this->sendText($user, $this->getJsonQueue());
	}
	
	/**
	 * Get the json string of the sale queue
	 * 
	 * @return string
	 */
	private function getJsonQueue()
	{
		$repItem = $this->_entityManager
			->getRepository('CudiBundle\Entity\Sales\ServingQueueItem');
			
		$repStatus = $this->_entityManager
			->getRepository('CudiBundle\Entity\Sales\ServingQueueStatus');
		
		$session = $this->_entityManager
		   ->getRepository('CudiBundle\Entity\Sales\Session')
		   ->findOpenSession();
		   
		return json_encode(
			array(
				'selling' => $this->createJsonObject($repItem->findAllByStatus($session, $repStatus->findOneByName('selling'))),
				'collected' => $this->createJsonObject($repItem->findAllByStatus($session, $repStatus->findOneByName('collected'))),
				'collecting' => $this->createJsonObject($repItem->findAllByStatus($session, $repStatus->findOneByName('collecting'))),
				'signed_in' => $this->createJsonObject($repItem->findAllByStatus($session, $repStatus->findOneByName('signed_in'))),
			)
		);
	}
	
	/**
	 * Return an array with the items in object
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	private function createJsonObject($items)
	{
		$results = array();
		foreach($items as $item) {
			$result = (object) array();
			$result->id = $item->getId();
			$result->number = $item->getQueueNumber();
			$result->name = $item->getPerson() ? $item->getPerson()->getFullName() : '';
			$results[] = $result;
		}
		return $results;
	}
}