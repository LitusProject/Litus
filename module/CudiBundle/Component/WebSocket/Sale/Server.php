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
     * @var \CudiBundle\Component\Websocket\Sale\Queue
     */
    private $_queue;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.queue_socket_file')
        );

        $this->_entityManager = $entityManager;
        $this->_queue = new Queue($entityManager);
    }

    /**
     * Parse received text
     *
     * @param \CommonBundle\Component\WebSockets\Sale\User $user
     * @param string                                       $data
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

        switch ($command->command) {
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

                if ('development' != getenv('APPLICATION_ENV')) {
                    if (!isset($command->authSession)) {
                        $this->removeUser($user);
                        $now = new DateTime();
                        echo '[' . $now->format('Y-m-d H:i:s') . '] WebSocket connection with invalid auth session.' . PHP_EOL;

                        return;
                    }

                    $authSession = $this->_entityManager
                        ->getRepository('CommonBundle\Entity\User\Session')
                        ->findOneById($command->authSession);

                    if ($authSession) {
                        $acl = new Acl($this->_entityManager);

                        $allowed = false;
                        foreach ($authSession->getPerson()->getRoles() as $role) {
                            if (
                                $role->isAllowed(
                                    $acl, 'cudi_sale_sale', 'sale'
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
     * @param integer                                $statusCode
     * @param string                                 $reason
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
            ->getRepository('CudiBundle\Entity\Sale\Session')
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

        foreach ($this->getUsers() as $user) {
            $session = $this->_entityManager
                ->getRepository('CudiBundle\Entity\Sale\Session')
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
     * @param mixed                                        $command
     */
    private function _gotAction(User $user, $command)
    {
        if (null == $user->getExtraData('session'))
            return;

        $session = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\Session')
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
            case 'startCollectingBulk':
                $this->_startCollecting($user, $command->id, true);
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
            case 'startSale':
                $this->_startSale($user, $command->id);
                $this->sendQueueItemToAll($command->id);
                break;
            case 'cancelSale':
                $this->_cancelSale($user, $command->id);
                $this->sendQueueItemToAll($command->id);
                break;
            case 'concludeSale':
                $this->_concludeSale($user, $command->id, $command->articles, $command->discounts, $command->payMethod);
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
                $this->_addArticle($user, $command->id, $command->articleId);
                break;
            case 'undoSale':
                $this->_undoSale($user, $command->id);
                $this->sendQueueItemToAll($command->id);
                break;
        }
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    private function _getCurrentAcademicYear()
    {
        return AcademicYear::getUniversityYear($this->_entityManager);
    }

    private function _signIn(User $user, $universityIdentification)
    {
        $session = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\Session')
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
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllAssignedByPerson($item->getPerson())
            );

            return $item->getId();
        }
    }

    private function _addToQueue(User $user, $universityIdentification)
    {
        $session = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOneById($user->getExtraData('session'));

        $item = $this->_queue->addPerson($session, $universityIdentification, true);

        if (is_string($item)) {
            $object = (object) array(
                'addPersonError' => json_decode($item),
            );
            $this->sendText($user, json_encode($object));
        } else {
            return $item->getId();
        }
    }

    private function _startCollecting(User $user, $id, $bulk = false)
    {
        $result = $this->_queue->startCollecting($user, $id, $bulk);
        if ($result)
            $this->sendText($user, $result);

        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        Printer::collectTicket(
            $this->_entityManager,
            $user->getExtraData('paydesk'),
            $item,
            $this->_entityManager
                ->getRepository('CudiBundle\Entity\Sale\Booking')
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

    private function _startSale(User $user, $id)
    {
        $this->sendText($user, $this->_queue->startSale($user, $id));
    }

    private function _cancelSale(User $user, $id)
    {
        $this->_queue->cancelSale($id);
    }

    private function _concludeSale(User $user, $id, $articles, $discounts, $payMethod)
    {
        $saleItems = $this->_queue->concludeSale($id, $articles, $discounts, $payMethod);

        if (null == $saleItems)
            return;

        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
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
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setComment($comment);
        $this->_entityManager->flush();
    }

    private function _addArticle(User $user, $id, $articleId)
    {
        $result = $this->_queue->addArticle($id, $articleId);
        if ($result)
            $this->sendText($user, $result);
    }

    private function _undoSale(User $user, $id)
    {
        $this->_queue->undoSale($id);

        $lightVersion = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.sale_light_version');

        if ($lightVersion == '1')
            $this->_startSale($user, $id);
    }
}
