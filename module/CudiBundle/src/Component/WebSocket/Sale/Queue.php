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
    CommonBundle\Entity\Users\Person,
	CudiBundle\Entity\Sales\Booking,
	CudiBundle\Entity\Sales\SaleItem,
	CudiBundle\Entity\Sales\QueueItem,
	CudiBundle\Entity\Sales\Session,
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
	 * @var array
	 */
	private $_lockedItems;
	
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
		$this->_lockedItems = array();
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

		if (strpos($data, 'action: ') === 0) {
			$this->_gotAction($user, $data);
		} elseif ($data == 'queueUpdated') {
			$this->sendQueueToAll();
		} elseif (strpos($data, 'initialize: ') === 0) {
		    $data = json_decode(substr($data, strlen('initialize: ')));
		    if (isset($data->session) && is_numeric($data->session))
		        $user->setExtraData('session', $data->session);
		    if (isset($data->queueType))
    		    $user->setExtraData('queueType', $data->queueType);
			$this->sendQueue($user);
		}
	}
	
	/**
	 * Do action when user closed his socket
	 *
	 * @param \CommonBundle\Component\WebSocket\User $user
	 * @param integer $statusCode
	 * @param string $reason
	 */
	protected function onClose(User $user, $statusCode, $reason)
	{
		foreach($this->_lockedItems as $key => $value) {
			if ($user == $value)
				break;
		}
		
		if (isset($key)) {
			unset($this->_lockedItems[$key]);
			parent::onClose($user, $statusCode, $reason);
			$this->sendQueueToAll();
		}
	}
	
	/**
	 * Parse action text
	 *
	 * @param \CommonBundle\Component\WebSockets\Sale\User $user
	 * @param string $data
	 */
	private function _gotAction(User $user, $data)
	{
		$action = substr($data, strlen('action: '), strpos($data, ' ', strlen('action: ')) - strlen('action: '));
		$params = trim(substr($data, strpos($data, ' ', strlen('action: ')) + 1));
		
		switch ($action) {
			case 'addToQueue':
				$result = $this->_addToQueue(
				    $this->_entityManager
				        ->getRepository('CudiBundle\Entity\Sales\Session')
				        ->findOneById($user->getExtraData('session')),
				    $params);
				$this->sendText($user, $result);
				break;
			case 'startCollecting':
				$this->_updateItemStatus($params, 'collecting');
				break;
			case 'cancelCollecting':
				$this->_updateItemStatus($params, 'signed_in');
				break;
			case 'stopCollecting':
				$this->_updateItemStatus($params, 'collected');
				break;
			case 'setHold':
				$this->_updateItemStatus($params, 'hold');
				break;
			case 'unsetHold':
				$this->_updateItemStatus($params, 'signed_in');
				break;
			case 'startSelling':
				$this->_updateItemStatus($params, 'selling');
				$this->sendText($user, $this->_getSaleInfo($user, $params));
				break;
			case 'cancelSelling':
				$this->_updateItemStatus($params, 'collected');

				unset($this->_lockedItems[$params]);
				$this->sendQueueToAll();
				break;
			case 'concludeSelling':
				$this->_concludeSelling(json_decode($params));
				break;
		}
		
		$this->sendQueueToAll();
	}
	
	/**
	 * Send queue to one user
	 *
	 * @param \CommonBundle\Component\WebSockets\Sale\User $user
	 */
	private function sendQueue(User $user)
	{
	    if (null == $user->getExtraData('session'))
	        return;
	        
	    $session = $this->_entityManager
	        ->getRepository('CudiBundle\Entity\Sales\Session')
	        ->findOneById($user->getExtraData('session'));
	    
		switch ($user->getExtraData('queueType')) {
			case 'fullQueue':
				$this->sendText($user, $this->_getJsonFullQueue($session));
				break;
			case 'shortQueue':
				$this->sendText($user, $this->_getJsonShortQueue($session));
				break;
		}
	}
	
	/**
	 * Send queue to all users
	 */
	private function sendQueueToAll()
	{
		foreach($this->getUsers() as $user)
			$this->sendQueue($user);
	}
	
	/**
	 * Add a person to the queue
	 *
	 * @param string $username
	 *
	 * @return string
	 */
	private function _addToQueue(Session $session, $username)
	{
		$person = $this->_entityManager
    		->getRepository('CommonBundle\Entity\Users\People\Academic')
    		->findOneByUsername($username);

    	if (null == $person) {
    		return json_encode(
    			(object) array(
    				'error' => 'person',
    			)
    		);
    	}
    	
    	$bookings = $this->_entityManager
    		->getRepository('CudiBundle\Entity\Sales\Booking')
    		->findAllAssignedByPerson($person);
    	
    	if (sizeof($bookings) == 0) {
	    	return json_encode(
	    		(object) array(
	    			'error' => 'noBookings',
	    		)
	    	);
    	}
    	
    	$queueItem = $this->_entityManager
    		->getRepository('CudiBundle\Entity\Sales\QueueItem')
    		->findOneByPersonNotSold($session, $person);
    	
    	if (null == $queueItem) {
    		$queueItem = new QueueItem($this->_entityManager, $person, $session);

    		$this->_entityManager->persist($queueItem);
    		$this->_entityManager->flush();
    	}
    	
    	return json_encode(
    		(object) array(
    			'queueNumber' => $queueItem->getQueueNumber(),
    		)
    	);
	}
	
	/**
	 * Get all the info in json for the sale
	 *
	 * @param \CommonBundle\Component\WebSockets\Sale\User $user
	 * @param int $itemId
	 *
	 * @return string
	 */
	private function _getSaleInfo(User $user, $itemId)
	{
		if (!is_numeric($itemId))
			return;
		
		$item = $this->_entityManager
			->getRepository('CudiBundle\Entity\Sales\QueueItem')
			->findOneById($itemId);
					
		if (!isset($item))
			return;
			
		$this->_lockedItems[$item->getId()] = $user;

		return json_encode(
			(object) array(
				'sale' => (object) array(
					'id' => $item->getId(),
					'person' => (object) array(
						'id' => $item->getPerson()->getId(),
						'name' => $item->getPerson()->getFullName(),
						'member' => $item->getPerson()->isMember(),
					),
					'articles' => $this->_createJsonBooking(
					    $this->_entityManager
    						->getRepository('CudiBundle\Entity\Sales\Booking')
    						->findAllOpenByPerson($item->getPerson()),
    					$item->getPerson()
    				)
				)
			)
		);
	}
	
	/**
	 * Return an array with the booking items in object
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	private function _createJsonBooking($items, Person $person)
	{
		$results = array();
		foreach($items as $item) {
			$result = (object) array(
			    'id' => $item->getId(),
			    'price' => $item->getArticle()->getSellPrice(),
			    'title' => $item->getArticle()->getMainArticle()->getTitle(),
			    'barcode' => $item->getArticle()->getBarcode(),
			    'author' => $item->getArticle()->getMainArticle()->getAuthors(),
			    'number' => $item->getNumber(),
			    'status' => $item->getStatus(),
			    'discounts' => array(),
			);
			foreach($item->getArticle()->getDiscounts() as $discount)
			    $result->discounts[$discount->getType()] = $discount->apply($item->getArticle()->getSellPrice());
			$results[] = $result;
		}
		return $results;
	}
	
	/**
	 * Get the json string of the full sale queue
	 *
	 * @param CudiBundle\Entity\Sales\Session
	 * 
	 * @return string
	 */
	private function _getJsonFullQueue(Session $session)
	{
		$repItem = $this->_entityManager
			->getRepository('CudiBundle\Entity\Sales\QueueItem');
		
		return json_encode(
			(object) array(
				'queue' => array(
					'selling' => $this->_createJsonQueue($repItem->findAllByStatus($session, 'selling')),
					'collected' => $this->_createJsonQueue($repItem->findAllByStatus($session, 'collected')),
					'collecting' => $this->_createJsonQueue($repItem->findAllByStatus($session, 'collecting')),
					'signed_in' => $this->_createJsonQueue($repItem->findAllByStatus($session, 'signed_in')),
				)
			)
		);
	}
	
	/**
	 * Get the json string of the short sale queue
     *
  	 * @param CudiBundle\Entity\Sales\Session
	 * 
	 * @return string
	 */
	private function _getJsonShortQueue(Session $session)
	{
		$repItem = $this->_entityManager
			->getRepository('CudiBundle\Entity\Sales\QueueItem');
		   
		return json_encode(
			(object) array(
				'queue' => $this->_createJsonQueue($repItem->findAllBySession($session))
			)
		);
	}
	
	/**
	 * Return an array with the queue items in object
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	private function _createJsonQueue($items)
	{
		$results = array();
		foreach($items as $item) {
			$result = (object) array();
			$result->id = $item->getId();
			$result->number = $item->getQueueNumber();
			$result->name = $item->getPerson() ? $item->getPerson()->getFullName() : '';
			$result->status = $item->getStatus();
			$result->locked = isset($this->_lockedItems[$item->getId()]);
			$results[] = $result;
		}
		return $results;
	}
	
	/**
	 * Update the status of a queue item
	 *
	 * @param int $itemId
	 * @param string $status
	 *
	 * @return array
	 */
	private function _updateItemStatus($itemId, $status)
	{
		if (!is_numeric($itemId))
			return;
			
		$item = $this->_entityManager
			->getRepository('CudiBundle\Entity\Sales\QueueItem')
			->findOneById($itemId);
			
		if (!isset($item))
			return;
		
		$item->setStatus($status);
		
		$this->_entityManager->flush();
	}
	
	/**
	 * Conclude a selling
	 *
	 * @param object $data
	 */
	private function _concludeSelling($data)
	{
		unset($this->_lockedItems[$data->id]);
		
		$queueItem = $this->_entityManager
			->getRepository('CudiBundle\Entity\Sales\QueueItem')
			->findOneById($data->id);
					
		if (!isset($queueItem))
			return;
			
		$bookings = $this->_entityManager
			->getRepository('CudiBundle\Entity\Sales\Booking')
			->findAllOpenByPerson($queueItem->getPerson());
		
		foreach($bookings as $booking) {
			$currentNumber = $data->articles->{$booking->getId()};
			if ($currentNumber > 0 && $currentNumber <= $booking->getNumber() && $booking->getStatus() == 'assigned') {
				if ($booking->getNumber() == $currentNumber) {
					$booking->setStatus('sold');
				} else {
					$remainder = new Booking($this->_entityManager, $booking->getPerson(), $booking->getArticle(), 'assigned', $booking->getNumber() - $currentNumber);
					$this->_entityManager->persist($remainder);
					$booking->setNumber($currentNumber)
						->setStatus('sold');
				}
				
				$price = $booking->getArticle()->getSellPrice();
				foreach($booking->getArticle()->getDiscounts() as $discount) {
				    if ($discount->getType() == $data->discount) {
				        if ($discount->getType() == 'member' && !$booking->getPerson()->isMember())
				            continue;
				        $price = $discount->apply($booking->getArticle()->getSellPrice());
				    }
				}
				
				$saleItem = new SaleItem(
				    $booking->getArticle(),
				    $currentNumber,
				    $price * $currentNumber / 100,
				    $queueItem
				);
				$this->_entityManager->persist($saleItem);
				
				$booking->getArticle()->setStockValue($booking->getArticle()->getStockValue() - $currentNumber);
			}
		}
		
		$this->_entityManager->flush();
		
		$this->_updateItemStatus($data->id, 'sold');
	}
}