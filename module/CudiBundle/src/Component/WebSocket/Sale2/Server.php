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

namespace CudiBundle\Component\WebSocket\Sale2;

use CommonBundle\Component\Util\AcademicYear,
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

        parent::__construct($address, $port-100);

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
                if (isset($command->session) && is_numeric($command->session))
                    $user->setExtraData('session', $command->session);
                if (isset($command->queueType))
                    $user->setExtraData('queueType', $command->queueType);
                if (isset($command->paydesk))
                    $user->setExtraData('paydesk', $command->paydesk);
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
                $this->_signIn($user, $command->universityIdentification);
                break;
            case 'addToQueue':
                $this->_addToQueue($user, $command->universityIdentification);
                break;
            case 'startCollecting':
                $this->_startCollecting($user, $command->id);
                break;
            case 'cancelCollecting':
                $this->_cancelCollecting($user, $command->id);
                break;
            case 'stopCollecting':
                $this->_stopCollecting($user, $command->id);
                break;
            case 'startSelling':
                $this->_startSelling($user, $command->id);
                break;
            case 'cancelSelling':
                $this->_cancelSelling($user, $command->id);
                break;
            case 'hold':
                $this->_hold($command->id);
                break;
            case 'unhold':
                $this->_unhold($command->id);
                break;
            case 'saveComment':
                $this->_saveComment($command->id, $command->comment);
                break;
        }

        $this->sendQueueToAll();
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

        $this->sendText($user, $this->_queue->addPerson($session, $universityIdentification));
        // TODO: print ticket
    }

    private function _addToQueue(User $user, $universityIdentification)
    {
        $session = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\Session')
            ->findOneById($user->getExtraData('session'));

        $this->_queue->addPerson($session, $universityIdentification);
    }

    private function _startCollecting(User $user, $id)
    {
        $result = $this->_queue->startCollecting($user, $id);
        if ($result)
            $this->sendText($user, $result);

        // TODO: print ticket
    }

    private function _stopCollecting(User $user, $id)
    {
        $this->_queue->stopCollecting($id);
    }

    private function _cancelCollecting(User $user, $id)
    {
        $this->_queue->cancelCollecting($id);
    }

    private function _startSelling(User $user, $id)
    {
        $this->_queue->startSelling($user, $id);
    }

    private function _cancelSelling(User $user, $id)
    {
        $this->_queue->cancelSelling($id);
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
}
