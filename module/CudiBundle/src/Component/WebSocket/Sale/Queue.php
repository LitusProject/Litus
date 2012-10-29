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

namespace CudiBundle\Component\WebSocket\Sale;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Component\WebSocket\User,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CommonBundle\Entity\Users\Person,
    CommonBundle\Entity\Users\Statuses\Organization as OrganizationStatus,
    CudiBundle\Entity\Sales\Booking,
    CudiBundle\Entity\Sales\SaleItem,
    CudiBundle\Entity\Sales\QueueItem,
    CudiBundle\Entity\Sales\Session,
    DateTime,
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
     * @var array
     */
    private $_collectedItems;

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
        $this->_collectedItems = array();
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
            if ($user == $value) {
                unset($this->_lockedItems[$key]);
                parent::onClose($user, $statusCode, $reason);
                $this->sendQueueToAll();
                break;
            }
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

        $now = new DateTime();

        $paydesk = $user->getExtraData('payDesk');
        if ($action !== 'setPayDesk' && $action !== 'addToQueue'  && empty($paydesk)) {
            $this->sendText($user, json_encode((object) array('error' => 'paydesk')));
            return;
        }

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
                $this->sendText($user, $this->_getCollectInfo($user, $params));
                break;
            case 'saveCollecting':
                $this->_saveCollecting(json_decode($params));
                break;
            case 'cancelCollecting':
                if (isset($this->_lockedItems[$params]))
                    unset($this->_lockedItems[$params]);
                $this->_updateItemStatus($params, 'signed_in');
                break;
            case 'stopCollecting':
                if (isset($this->_lockedItems[$params]))
                    unset($this->_lockedItems[$params]);
                $this->_updateItemStatus($params, 'collected');
                break;
            case 'setHold':
                $this->_updateItemStatus($params, 'hold');
                break;
            case 'unsetHold':
                $this->_updateItemStatus($params, 'signed_in');
                break;
            case 'startSelling':
                $this->_updateItemStatus($params, 'selling', $user);
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
            case 'undoSelling':
                $this->_undoSelling(json_decode($params));
                break;
            case 'setPayDesk':
                $user->setExtraData('payDesk', trim($params));
                break;
        }
        if ($action !== 'setPayDesk')
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
     * Save collected items
     *
     * @param object $data
     */
    private function _saveCollecting($data)
    {
        $this->_collectedItems[$data->id] = $data->articles;
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

        $registration = $this->_entityManager
            ->getRepository('SecretaryBundle\Entity\Registration')
            ->findOneByAcademicAndAcademicYear($person, $this->_getCurrentAcademicYear());

        $metaData = $this->_entityManager
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($person, $this->_getCurrentAcademicYear());

        if (empty($bookings) && !(null !== $registration && !$registration->hasPayed() && $metaData->becomeMember())) {
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

        $this->_printQueueTicket($queueItem, 'signin');

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
                    'comment' => $item->getComment(),
                    'person' => (object) array(
                        'id' => $item->getPerson()->getId(),
                        'name' => $item->getPerson()->getFullName(),
                        'university_identification' => $item->getPerson()->getUniversityIdentification(),
                        'member' => $item->getPerson()->isMember($this->_getCurrentAcademicYear()),
                    ),
                    'articles' => $this->_createJsonBooking(
                        $this->_entityManager
                            ->getRepository('CudiBundle\Entity\Sales\Booking')
                            ->findAllOpenByPerson($item->getPerson()),
                        $item
                    )
                )
            )
        );
    }

    /**
     * Get all the info in json for the collecting
     *
     * @param \CommonBundle\Component\WebSockets\Sale\User $user
     * @param int $itemId
     *
     * @return string
     */
    private function _getCollectInfo(User $user, $itemId)
    {
        $enableCollectScanning = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_collect_scanning');

        if (!is_numeric($itemId))
            return;

        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($itemId);

        if (!isset($item))
            return;

        $this->_printCollectTicket($item, $user->getExtraData('payDesk'));

        if ($enableCollectScanning !== '1')
            return;

        $this->_lockedItems[$item->getId()] = $user;

        return json_encode(
            (object) array(
                'collecting' => (object) array(
                    'id' => $item->getId(),
                    'comment' => $item->getComment(),
                    'person' => (object) array(
                        'id' => $item->getPerson()->getId(),
                        'name' => $item->getPerson()->getFullName(),
                        'university_identification' => $item->getPerson()->getUniversityIdentification(),
                        'member' => $item->getPerson()->isMember($this->_getCurrentAcademicYear()),
                    ),
                    'articles' => $this->_createJsonBooking(
                        $this->_entityManager
                            ->getRepository('CudiBundle\Entity\Sales\Booking')
                            ->findAllOpenByPerson($item->getPerson()),
                        $item
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
    private function _createJsonBooking($items, QueueItem $queueItem)
    {
        $person = $queueItem->getPerson();
        $results = array();

        $registration = $this->_entityManager
            ->getRepository('SecretaryBundle\Entity\Registration')
            ->findOneByAcademicAndAcademicYear($person, $this->_getCurrentAcademicYear());

        $metaData = $this->_entityManager
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($person, $this->_getCurrentAcademicYear());

        if (null !== $registration && !$registration->hasPayed() && $metaData->becomeMember()) {
            $collected = 0;
            if (isset($this->_collectedItems[$queueItem->getId()])) {
                foreach($this->_collectedItems[$queueItem->getId()] as $id => $booking) {
                    if ($id == 'membership')
                        $collected = $booking;
                }
            }

            $results[] = (object) array(
                'id' => 'membership',
                'price' => $this->_entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('secretary.membership_price'),
                'title' => 'Membership',
                'barcode' => '',
                'barcodes' => array(),
                'author' => $this->_entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('union_name'),
                'number' => 1,
                'status' => 'assigned',
                'collected' => $collected,
                'discounts' => array(),
            );
        }

        foreach($items as $item) {
            $barcodes = array($item->getArticle()->getBarcode());
            foreach($item->getArticle()->getAdditionalBarcodes() as $barcode)
                $barcodes[] = $barcode->getBarcode();

            $collected = 0;
            if (isset($this->_collectedItems[$queueItem->getId()])) {
                foreach($this->_collectedItems[$queueItem->getId()] as $id => $booking) {
                    if ($id == $item->getId())
                        $collected = $booking;
                }
            }

            $result = (object) array(
                'id' => $item->getId(),
                'price' => $item->getArticle()->getSellPrice(),
                'title' => $item->getArticle()->getMainArticle()->getTitle(),
                'barcode' => $item->getArticle()->getBarcode(),
                'barcodes' => $barcodes,
                'author' => $item->getArticle()->getMainArticle()->getAuthors(),
                'number' => $item->getNumber(),
                'status' => $item->getStatus(),
                'collected' => $collected,
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
            $result->university_identification = $item->getPerson()->getUniversityIdentification();
            $result->status = $item->getStatus();
            $result->locked = isset($this->_lockedItems[$item->getId()]);

            if ($item->getPayDesk())
                $result->payDesk = $item->getPayDesk()->getName();
            $results[] = $result;
        }
        return $results;
    }

    /**
     * Update the status of a queue item
     *
     * @param int $itemId
     * @param string $status
     * @param CommonBundle\Component\WebSocket\User|null $user
     *
     * @return array
     */
    private function _updateItemStatus($itemId, $status, User $user = null)
    {
        if (!is_numeric($itemId))
            return;

        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($itemId);

        if (!isset($item))
            return;

        if ('selling' == $status && isset($user)) {
            $paydesk = $this->_entityManager
                ->getRepository('CudiBundle\Entity\Sales\PayDesk')
                ->findOneByCode($user->getExtraData('payDesk'));

            if ($paydesk) {
                $item->setPayDesk($paydesk);
            }
        }

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

        $articles = array();
        $prices = array();
        $barcodes = array();
        $totalPrice = 0;

        if (isset($data->articles->membership) && 1 == $data->articles->membership) {
            $queueItem->getPerson()
                ->addOrganizationStatus(
                    new OrganizationStatus(
                        $queueItem->getPerson(),
                        'member',
                        $this->_getCurrentAcademicYear()
                    )
                );
            $registration = $this->_entityManager
                ->getRepository('SecretaryBundle\Entity\Registration')
                ->findOneByAcademicAndAcademicYear($queueItem->getPerson(), $this->_getCurrentAcademicYear());
            $registration->setPayed();
            $this->_entityManager->flush();

            $price =$this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_price');
            $articles[] = 'Membership';
            $prices[] = (string) number_format($price / 100, 2);
            $barcodes[] = '';
            $totalPrice += $price;
        }

        $queueItem->setPayMethod($data->payMethod);

        $bookings = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findAllOpenByPerson($queueItem->getPerson());

        foreach($bookings as $booking) {
            if (!isset($data->articles->{$booking->getId()}))
                continue;

            $currentNumber = $data->articles->{$booking->getId()};
            if ($currentNumber > 0 && $currentNumber <= $booking->getNumber() && $booking->getStatus() == 'assigned') {
                if ($booking->getNumber() == $currentNumber) {
                    $booking->setStatus('sold', $this->_entityManager);
                } else {
                    $remainder = new Booking($this->_entityManager, $booking->getPerson(), $booking->getArticle(), 'assigned', $booking->getNumber() - $currentNumber);
                    $this->_entityManager->persist($remainder);
                    $booking->setNumber($currentNumber)
                        ->setStatus('sold', $this->_entityManager);
                }

                $price = $booking->getArticle()->getSellPrice();
                foreach($booking->getArticle()->getDiscounts() as $discount) {
                    if ($discount->getType() == $data->discount) {
                        if ($discount->getType() == 'member' && !$booking->getPerson()->isMember($this->_getCurrentAcademicYear()))
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

                $articles[] = ($currentNumber > 1 ?' (' . $currentNumber . 'x)' : '') . $booking->getArticle()->getMainArticle()->getTitle();
                $prices[] = (string) number_format($price * $currentNumber / 100, 2);
                $barcodes[] = $booking->getArticle()->getBarcode();
                $totalPrice += $price * $currentNumber;
            }
        }

        Printer::salePrint(
            $this->_entityManager,
            $queueItem->getPayDesk()->getCode(),
            $queueItem->getPerson()->getUniversityIdentification(),
            (int) $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.queue_item_barcode_prefix') + $queueItem->getId(),
            $queueItem->getQueueNumber(),
            (string) number_format($totalPrice / 100, 2),
            $articles,
            $prices,
            $barcodes
        );

        $this->_entityManager->flush();

        $this->_updateItemStatus($data->id, 'sold');
    }

    private function _undoSelling($data)
    {
        $queueItem = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($data->id);

        if (!isset($queueItem))
            return;

        $queueItem->setPayMethod(null);

        $saleItems = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\SaleItem')
            ->findByQueueItem($queueItem);

        foreach($saleItems as $saleItem) {
            $booking = $this->_entityManager
                ->getRepository('CudiBundle\Entity\Sales\Booking')
                ->findOneSoldByArticleAndNumber($saleItem->getArticle(), $saleItem->getNumber());

            if (isset($booking))
                $booking->setStatus('assigned', $this->_entityManager);

            $saleItem->getArticle()->setStockValue($saleItem->getArticle()->getStockValue() + $saleItem->getNumber());
        }

        $this->_entityManager->flush();

        $this->_updateItemStatus($data->id, 'collected');
    }

    private function _getCurrentAcademicYear()
    {
        $startAcademicYear = AcademicYear::getStartOfAcademicYear();
        $startAcademicYear->setTime(0, 0);

        $academicYear = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);

        if (null === $academicYear) {
            $organizationStart = str_replace(
                '{{ year }}',
                $startAcademicYear->format('Y'),
                $this->_entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('start_organization_year')
            );
            $organizationStart = new DateTime($organizationStart);
            $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
            $this->_entityManager->persist($academicYear);
            $this->_entityManager->flush();
        }

        return $academicYear;
    }

    private function _getPrintInfo(QueueItem $item)
    {
        $bookings = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findAllOpenByPerson($item->getPerson());

        $articles = array();
        $prices = array();
        $barcodes = array();
        $totalPrice = 0;

        $registration = $this->_entityManager
            ->getRepository('SecretaryBundle\Entity\Registration')
            ->findOneByAcademicAndAcademicYear($item->getPerson(), $this->_getCurrentAcademicYear());

        $metaData = $this->_entityManager
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($item->getPerson(), $this->_getCurrentAcademicYear());

        if (null !== $registration && !$registration->hasPayed() && $metaData->becomeMember()) {
            $price =$this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_price');
            $articles[] = 'Membership';
            $prices[] = (string) number_format($price / 100, 2);
            $barcodes[] = '';
            $totalPrice += $price;
        }

        if (count($bookings > 0)) {
            foreach($bookings as $booking) {
                if ($booking->getStatus() != 'assigned')
                    continue;
                $articles[] = ($booking->getNumber() > 1 ?' (' . $booking->getNumber() . 'x)' : '') . $booking->getArticle()->getMainArticle()->getTitle();
                $prices[] = (string) number_format($booking->getArticle()->getSellPrice() * $booking->getNumber() / 100, 2);
                $barcodes[] = $booking->getArticle()->getBarcode();
                $totalPrice += $booking->getArticle()->getSellPrice() * $booking->getNumber();
            }
        }

        return array($totalPrice, $articles, $prices, $barcodes);
    }

    private function _printQueueTicket(QueueItem $item, $printer)
    {
        list($totalPrice, $articles, $prices, $barcodes) = $this->_getPrintInfo($item);

        Printer::queuePrint(
            $this->_entityManager,
            $printer,
            $item->getPerson()->getUniversityIdentification(),
            (int) $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.queue_item_barcode_prefix') + $item->getId(),
            $item->getQueueNumber(),
            (string) number_format($totalPrice / 100, 2),
            $articles,
            $prices,
            $barcodes
        );
    }

    private function _printCollectTicket(QueueItem $item, $printer)
    {
        list($totalPrice, $articles, $prices, $barcodes) = $this->_getPrintInfo($item);

        Printer::collectPrint(
            $this->_entityManager,
            $printer,
            $item->getPerson()->getUniversityIdentification(),
            (int) $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.queue_item_barcode_prefix') + $item->getId(),
            $item->getQueueNumber(),
            (string) number_format($totalPrice / 100, 2),
            $articles,
            $prices,
            $barcodes
        );
    }
}
