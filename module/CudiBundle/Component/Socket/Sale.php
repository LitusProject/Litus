<?php

namespace CudiBundle\Component\Socket;

use CommonBundle\Component\Console\Command;
use CommonBundle\Component\Socket\User;
use CudiBundle\Component\Socket\Sale\Printer;
use CudiBundle\Component\Socket\Sale\Queue;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;

/**
 * This is the server to handle all requests by the WebSocket protocol
 * for the Queue.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Sale extends \CommonBundle\Component\Socket\Socket
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param LoopInterface           $loop
     * @param Command                 $command
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, LoopInterface $loop, Command $command)
    {
        parent::__construct($serviceLocator, $loop, $command);

        $this->queue = new Queue($this->getEntityManager());
    }

    /**
     * @param ConnectionInterface $from
     * @param string              $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $this->getEntityManager()->clear();

        $command = json_decode($msg);
        if ($command === null) {
            $this->writeln('<comment>Failed to decode received message</comment>');
            return;
        }

        $user = $this->getUser($from);
        if ($user === null) {
            $this->writeln('<info>Received message for unrecognized connection</info>');
            return;
        }

        switch ($command->command) {
            case 'initialize':
                $this->initialize($user, $command);
                break;

            case 'action':
                if (!$user->isAuthenticated()) {
                    break;
                }

                $this->handleAction($user, $command);
                break;

            default:
                $this->writeln('Received invalid command <comment>' . $command->command . '</comment>');
        }
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->queue->unlock(
            $this->getUser($conn)
        );
        $this->sendQueueToAll();

        parent::onClose($conn);
    }

    /**
     * Perform client initialization
     *
     * @param User  $user
     * @param mixed $command
     */
    private function initialize(User $user, $command)
    {
        $key = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_socket_key');

        if (!isset($command->key) || $command->key != $key) {
            $this->writeln('<error>Connection with invalid key</error>');
            $this->removeUser($user);

            return;
        }

        if (!isset($command->authSession) || !$user->authenticate($command->authSession)) {
            $this->writeln('<error>Failed to authenticate user</error>');
            $this->removeUser($user);

            return;
        }

        if (!$user->isAllowed('cudi_sale_sale', 'sale')) {
            $this->writeln('<error>Unauthorized connection</error>');
            $this->removeUser($user);

            return;
        }

        if (isset($command->session)) {
            $user->session = $command->session;
        }

        if (isset($command->queueType)) {
            $user->queueType = $command->queueType;
        }

        if (isset($command->paydesk)) {
            $user->paydesk = $command->paydesk;
        }

        $this->sendQueue($user);
    }

    /**
     * Handle action specified by client
     *
     * @param User  $user
     * @param mixed $command
     */
    private function handleAction(User $user, $command)
    {
        if (!isset($user->session)) {
            return;
        }

        switch ($command->action) {
            case 'signIn':
                $id = $this->signIn($user, $command->universityIdentification);
                $this->sendQueueItemToAll($id);

                break;

            case 'addToQueue':
                $id = $this->addToQueue($user, $command->universityIdentification);
                $this->sendQueueItemToAll($id);

                break;

            case 'startCollecting':
                $this->startCollecting($user, $command->id);
                $this->sendQueueItemToAll($command->id);

                break;

            case 'startCollectingBulk':
                $this->startCollecting($user, $command->id, true);
                $this->sendQueueItemToAll($command->id);

                break;

            case 'cancelCollecting':
                $this->cancelCollecting($command->id);
                $this->sendQueueItemToAll($command->id);

                break;

            case 'stopCollecting':
                $this->stopCollecting($command->id, $command->articles ?? null);
                $this->sendQueueItemToAll($command->id);

                break;

            case 'startSale':
                $this->startSale($user, $command->id);
                $this->sendQueueItemToAll($command->id);

                break;

            case 'cancelSale':
                $this->cancelSale($command->id);
                $this->sendQueueItemToAll($command->id);

                break;

            case 'concludeSale':
                $this->concludeSale($user, $command->id, $command->articles, $command->discounts, $command->payMethod);
                $this->sendQueueItemToAll($command->id);

                break;

            case 'hold':
                $this->hold($command->id);
                $this->sendQueueItemToAll($command->id);

                break;

            case 'unhold':
                $this->unhold($command->id);
                $this->sendQueueItemToAll($command->id);

                break;

            case 'saveComment':
                $this->saveComment($command->id, $command->comment);
                $this->sendQueueItemToAll($command->id);

                break;

            case 'addArticle':
                $this->addArticle($user, $command->id, $command->articleId);
                $this->sendQueueItemToAll($command->id);

                break;

            case 'undoSale':
                $this->undoSale($user, $command->id);
                $this->sendQueueItemToAll($command->id);

                break;

            case 'cancelArticle':
                $this->cancelBooking($command->bookingId);
                $this->sendQueueItemToAll($command->id);

                break;

            default:
                $this->writeln('Received invalid action <comment>' . $command->action . '</comment>');
        }
    }

    /**
     * Send queue to one user
     *
     * @param  User $user
     * @return null
     */
    private function sendQueue(User $user)
    {
        if (!isset($user->session)) {
            return;
        }

        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOneById($user->session);

        switch ($user->queueType) {
            case 'queue':
                $user->send(
                    $this->queue->getJsonQueue($session)
                );

                break;

            case 'queueList':
                $user->send(
                    $this->queue->getJsonQueueList($session)
                );

                break;
        }
    }

    /**
     * @return null
     */
    private function sendQueueToAll()
    {
        foreach ($this->getUsers() as $user) {
            $this->sendQueue($user);
        }
    }

    /**
     * Send queue to all users
     * @param  integer $id
     * @return null
     */
    private function sendQueueItemToAll($id)
    {
        if ($id === null) {
            return;
        }

        foreach ($this->getUsers() as $user) {
            if (!isset($user->session)) {
                continue;
            }

            $session = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session')
                ->findOneById($user->session);

            switch ($user->queueType) {
                case 'queue':
                    $user->send(
                        $this->queue->getJsonQueue($session)
                    );

                    break;

                case 'queueList':
                    $user->send(
                        $this->queue->getJsonQueueItem($id)
                    );

                    break;
            }
        }
    }

    /**
     * @param  User   $user
     * @param  string $universityIdentification
     * @return integer|null
     */
    private function signIn(User $user, $universityIdentification)
    {
        if (!isset($user->session)) {
            return;
        }

        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOneById($user->session);

        $item = $this->queue->addPerson($session, $universityIdentification);
        if (is_string($item)) {
            $user->send($item);
        } else {
            $user->send(
                json_encode(
                    (object) array(
                        'queueNumber' => $item->getQueueNumber(),
                    )
                )
            );

            Printer::signInTicket(
                $this->getEntityManager(),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.signin_printer'),
                $item,
                $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllAssignedByPerson($item->getPerson())
            );

            return $item->getId();
        }
    }

    /**
     * @param  User   $user
     * @param  string $universityIdentification
     * @return integer|null
     */
    private function addToQueue(User $user, $universityIdentification)
    {
        if (!isset($user->session)) {
            return;
        }

        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOneById($user->session);

        $item = $this->queue->addPerson($session, $universityIdentification, true);
        if (is_string($item)) {
            $object = (object) array(
                'addPersonError' => json_decode($item),
            );
            $user->send(json_encode($object));
        } else {
            return $item->getId();
        }
    }

    /**
     * @param  User    $user
     * @param  integer $id
     * @param  boolean $bulk
     * @return null
     */
    private function startCollecting(User $user, $id, $bulk = false)
    {
        $result = $this->queue->startCollecting($user, $id, $bulk);
        if ($result !== null) {
            $user->send($result);
        }

        $item = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        Printer::collectTicket(
            $this->getEntityManager(),
            $user->paydesk,
            $item,
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findAllAssignedByPerson($item->getPerson())
        );
    }

    /**
     * @param  integer    $id
     * @param  array|null $articles
     * @return null
     */
    private function stopCollecting($id, $articles = null)
    {
        $this->queue->stopCollecting($id, $articles);
    }

    /**
     * @param  integer $id
     * @return null
     */
    private function cancelCollecting($id)
    {
        $this->queue->cancelCollecting($id);
    }

    /**
     * @param  User    $user
     * @param  integer $id
     * @return null
     */
    private function startSale(User $user, $id)
    {
        $user->send(
            $this->queue->startSale($user, $id)
        );
    }

    /**
     * @param  integer $id
     * @return null
     */
    private function cancelSale($id)
    {
        $this->queue->cancelSale($id);
    }

    /**
     * @param  User    $user
     * @param  integer $id
     * @param  array   $articles
     * @param  array   $discounts
     * @param  string  $payMethod
     * @return null
     */
    private function concludeSale(User $user, $id, $articles, $discounts, $payMethod)
    {
        $saleItems = $this->queue->concludeSale($id, $articles, $discounts, $payMethod);
        if ($saleItems === null) {
            return;
        }

        $item = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        Printer::saleTicket(
            $this->getEntityManager(),
            $user->paydesk,
            $item,
            $saleItems
        );
    }

    /**
     * @param  integer $id
     * @return null
     */
    private function hold($id)
    {
        $this->queue->setHold($id);
    }

    /**
     * @param  integer $id
     * @return null
     */
    private function unhold($id)
    {
        $this->queue->setUnhold($id);
    }

    /**
     * @param  integer $id
     * @param  string  $comment
     * @return null
     */
    private function saveComment($id, $comment)
    {
        $item = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setComment($comment);

        $this->getEntityManager()->flush();
    }

    /**
     * @param  User    $user
     * @param  integer $id
     * @param  integer $articleId
     * @return null
     */
    private function addArticle(User $user, $id, $articleId)
    {
        $user->send(
            $this->queue->addArticle($id, $articleId)
        );
    }

    /**
     * @param  User    $user
     * @param  integer $id
     * @return null
     */
    private function undoSale(User $user, $id)
    {
        $this->queue->undoSale($id);

        $lightVersion = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.sale_light_version');

        if ($lightVersion == '1') {
            $this->startSale($user, $id);
        }
    }

    /**
     * @param integer $booking_id
     * @return void
     */
    private function cancelBooking($booking_id)
    {
        $booking = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findOneById($booking_id);

        if ($booking === null) {
            // Happens when article is added by cudi, this is not saved in database so has no booking_id
            // No problem here, line with article is just removed in UI
            $this->writeln('No booking found');
            return;
        }

        if (!$booking->getArticle()->isUnbookable()) {
            // Should never occur, cancel button is only active when isUnbookable() == true
            $this->writeln('Booking not cancellable: '. $booking_id);
            return;
        }

        $booking->setStatus('canceled', $this->getEntityManager());
        $this->getEntityManager()->flush();

        $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->assignAllByArticle($booking->getArticle(), $this->getMailTransport());
    }
}
