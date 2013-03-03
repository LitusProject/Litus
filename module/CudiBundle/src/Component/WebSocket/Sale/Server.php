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

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Component\WebSocket\User,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    DateTime,
    Doctrine\ORM\EntityManager;

/**
 * This is the server to handle all requests by the websocket protocol for the Queue.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Server extends \CommonBundle\Component\WebSocket\Server
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @var \CudiBundle\Component\Websocket\Sale2\Queue
     */
    private $_queue;

    /**
     * @param Doctrine\ORM\EntityManager $entityManager
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
        $this->_queue = new Queue($entityManager);
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

        $key = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_socket_key');

        $command = json_decode($data);

        if (null == $command)
            return;

        switch($command->command) {
            case 'action':
                if ($this->isAuthenticated($user->getSocket()))
                    $this->_gotAction($user, $command);
                break;
            case 'queueUpdated':
                $this->sendQueueToAll();
                break;
            case 'initialize':
                if (!isset($command->key) || $command->key != $key) {
                    $this->removeUser($user);
                    $now = new DateTime();
                    echo '[' . $now->format('Y-m-d H:i:s') . '] WebSocket connection with invalid key.' . PHP_EOL;
                    return;
                }

                if (!isset($command->authSession)) {
                    $this->removeUser($user);
                    $now = new DateTime();
                    echo '[' . $now->format('Y-m-d H:i:s') . '] WebSocket connection with invalid auth session.' . PHP_EOL;
                    return;
                }

                $authSession = $this->_entityManager
                    ->getRepository('CommonBundle\Entity\Users\Session')
                    ->findOneById($command->authSession);

                if ($authSession) {
                    $acl = new Acl($this->_entityManager);

                    $allowed = false;
                    foreach ($authSession->getPerson()->getRoles() as $role) {
                        if (
                            $role->isAllowed(
                                $acl, 'sale_sale', 'sale'
                            )
                        ) {
                            $allowed = true;
                        }
                    }
                }

                if (null == $authSession || !$allowed) {
                    $this->removeUser($user);
                    $now = new DateTime();
                    echo '[' . $now->format('Y-m-d H:i:s') . '] WebSocket connection with invalid auth session.' . PHP_EOL;
                    return;
                }

                if (isset($command->session) && is_numeric($command->session))
                    $user->setExtraData('session', $command->session);
                if (isset($command->queueType))
                    $user->setExtraData('queueType', $command->queueType);
                if (isset($command->paydesk))
                    $user->setExtraData('paydesk', $command->paydesk);

                $this->addAuthenticated($user->getSocket());
                $this->sendQueue($user);
                break;
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
        $this->_queue->unlockByUser($user);
        parent::onClose($user, $statusCode, $reason);
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
            case 'queue':
                $this->sendText($user, $this->_queue->getJsonQueue($session));
                break;
            case 'queueList':
                $this->sendText($user, $this->_queue->getJsonQueueList($session));
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
     * Send queue to all users
     */
    private function sendQueueItemToAll($id)
    {
        if (null == $id)
            return;

        $json = $this->_queue->getJsonQueueItem($id);

        foreach($this->getUsers() as $user) {
            $session = $this->_entityManager
                ->getRepository('CudiBundle\Entity\Sales\Session')
                ->findOneById($user->getExtraData('session'));

            switch ($user->getExtraData('queueType')) {
                case 'queue':
                    $this->sendText($user, $this->_queue->getJsonQueue($session));
                    break;
                case 'queueList':
                    $this->sendText($user, $json);
                    break;
            }
        }
    }

    /**
     * Parse action text
     *
     * @param \CommonBundle\Component\WebSockets\Sale\User $user
     * @param mixed $command
     */
    private function _gotAction(User $user, $command)
    {
        if (null == $user->getExtraData('session'))
            return;

        $session = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\Session')
            ->findOneById($user->getExtraData('session'));

        switch ($command->action) {
            case 'signIn':
                $this->sendQueueToAll($this->_signIn($user, $command->universityIdentification));
                break;
            case 'addToQueue':
                $this->sendQueueItemToAll($this->_addToQueue($user, $command->universityIdentification));
                break;
            case 'startCollecting':
                $this->_startCollecting($user, $command->id);
                $this->sendQueueItemToAll($command->id);
                break;
            case 'cancelCollecting':
                $this->_cancelCollecting($user, $command->id);
                $this->sendQueueItemToAll($command->id);
                break;
            case 'stopCollecting':
                $this->_stopCollecting($user, $command->id, isset($command->articles) ? $command->articles : null);
                $this->sendQueueItemToAll($command->id);
                break;
            case 'startSelling':
                $this->_startSelling($user, $command->id);
                $this->sendQueueItemToAll($command->id);
                break;
            case 'cancelSelling':
                $this->_cancelSelling($user, $command->id);
                $this->sendQueueItemToAll($command->id);
                break;
            case 'concludeSelling':
                $this->_concludeSelling($user, $command->id, $command->articles, $command->discounts, $command->payMethod);
                $this->sendQueueItemToAll($command->id);
                break;
            case 'hold':
                $this->_hold($command->id);
                $this->sendQueueItemToAll($command->id);
                break;
            case 'unhold':
                $this->_unhold($command->id);
                $this->sendQueueItemToAll($command->id);
                break;
            case 'saveComment':
                $this->_saveComment($command->id, $command->comment);
                break;
            case 'addArticle':
                $this->_addArticle($user, $command->id, $command->barcode);
                break;
            case 'undoSelling':
                $this->_undoSelling($command->id);
                $this->sendQueueItemToAll($command->id);
                break;
        }
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
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

    private function _signIn(User $user, $universityIdentification)
    {
        $session = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\Session')
            ->findOneById($user->getExtraData('session'));

        $item = $this->_queue->addPerson($session, $universityIdentification);

        if (is_string($item)) {
            $this->sendText($user, $item);
        } else {
            $this->sendText(
                $user,
                json_encode(
                    (object) array(
                        'queueNumber' => $item->getQueueNumber(),
                    )
                )
            );

            Printer::signInTicket(
                $this->_entityManager,
                'signin',
                $item,
                $this->_entityManager
                    ->getRepository('CudiBundle\Entity\Sales\Booking')
                    ->findAllAssignedByPerson($item->getPerson())
            );

            return $item->getId();
        }
    }

    private function _addToQueue(User $user, $universityIdentification)
    {
        $session = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\Session')
            ->findOneById($user->getExtraData('session'));

        $item = $this->_queue->addPerson($session, $universityIdentification, true);

        if (is_string($item)) {
            $this->sendText($user, $item);
        } else {
            return $item->getId();
        }
    }

    private function _startCollecting(User $user, $id)
    {
        $result = $this->_queue->startCollecting($user, $id);
        if ($result)
            $this->sendText($user, $result);

        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($id);

        Printer::collectTicket(
            $this->_entityManager,
            $user->getExtraData('paydesk'),
            $item,
            $this->_entityManager
                ->getRepository('CudiBundle\Entity\Sales\Booking')
                ->findAllAssignedByPerson($item->getPerson())
        );
    }

    private function _stopCollecting(User $user, $id, $articles = null)
    {
        $this->_queue->stopCollecting($id, $articles);
    }

    private function _cancelCollecting(User $user, $id)
    {
        $this->_queue->cancelCollecting($id);
    }

    private function _startSelling(User $user, $id)
    {
        $this->sendText($user, $this->_queue->startSelling($user, $id));
    }

    private function _cancelSelling(User $user, $id)
    {
        $this->_queue->cancelSelling($id);
    }

    private function _concludeSelling(User $user, $id, $articles, $discounts, $payMethod)
    {
        $saleItems = $this->_queue->concludeSelling($id, $articles, $discounts, $payMethod);

        if (null == $saleItems)
            return;

        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($id);

        Printer::saleTicket(
            $this->_entityManager,
            $user->getExtraData('paydesk'),
            $item,
            $saleItems
        );
    }

    private function _hold($id)
    {
        $this->_queue->setHold($id);
    }

    private function _unhold($id)
    {
        $this->_queue->setUnhold($id);
    }

    private function _saveComment($id, $comment)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($id);

        $item->setComment($comment);
        $this->_entityManager->flush();
    }

    private function _addArticle(User $user, $id, $barcode)
    {
        $result = $this->_queue->addArticle($id, $barcode);
        if ($result)
            $this->sendText($user, $result);
    }

    private function _undoSelling($id)
    {
        $this->_queue->undoSelling($id);
    }
}
