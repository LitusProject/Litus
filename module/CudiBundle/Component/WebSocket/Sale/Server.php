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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Component\WebSocket\Sale;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Component\WebSocket\User,
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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.queue_socket_file')
        );

        $this->entityManager = $entityManager;
        $this->queue = new Queue($entityManager);
    }

    /**
     * Parse received text
     *
     * @param User   $user
     * @param string $data
     */
    protected function gotText(User $user, $data)
    {
        $this->entityManager->clear();

        $key = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_socket_key');

        $command = json_decode($data);

        if (null == $command) {
            return;
        }

        switch ($command->command) {
            case 'action':
                if ($this->isAuthenticated($user->getSocket())) {
                    $this->gotAction($user, $command);
                }
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

                    $authSession = $this->entityManager
                        ->getRepository('CommonBundle\Entity\User\Session')
                        ->findOneById($command->authSession);

                    $allowed = false;
                    if ($authSession) {
                        $acl = new Acl($this->entityManager);

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

                if (isset($command->session) && is_numeric($command->session)) {
                    $user->setExtraData('session', $command->session);
                }
                if (isset($command->queueType)) {
                    $user->setExtraData('queueType', $command->queueType);
                }
                if (isset($command->paydesk)) {
                    $user->setExtraData('paydesk', $command->paydesk);
                }

                $this->addAuthenticated($user->getSocket());
                $this->sendQueue($user);
                break;
        }
    }

    /**
     * Parse received binary
     *
     * @param  User   $user
     * @param  string $data
     * @return null
     */
    protected function gotBin(User $user, $data)
    {
    }

    /**
     * Do action when user closed his socket
     *
     * @param  User    $user
     * @param  integer $statusCode
     * @param  string  $reason
     * @return null
     */
    protected function onClose(User $user, $statusCode, $reason)
    {
        $this->queue->unlockByUser($user);
        parent::onClose($user, $statusCode, $reason);
        $this->sendQueueToAll();
    }

    /**
     * Do action when a new user has connected to this socket
     *
     * @param  User $user
     * @return null
     */
    protected function onConnect(User $user)
    {
    }

    /**
     * Send queue to one user
     *
     * @param  User $user
     * @return null
     */
    private function sendQueue(User $user)
    {
        if (null == $user->getExtraData('session')) {
            return;
        }

        $session = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOneById($user->getExtraData('session'));

        switch ($user->getExtraData('queueType')) {
            case 'queue':
                $this->sendText($user, $this->queue->getJsonQueue($session));
                break;
            case 'queueList':
                $this->sendText($user, $this->queue->getJsonQueueList($session));
                break;
        }
    }

    /**
     * Send queue to all users
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
     * @param  int  $id
     * @return null
     */
    private function sendQueueItemToAll($id)
    {
        if (null === $id) {
            return;
        }

        $json = $this->queue->getJsonQueueItem($id);

        foreach ($this->getUsers() as $user) {
            $session = $this->entityManager
                ->getRepository('CudiBundle\Entity\Sale\Session')
                ->findOneById($user->getExtraData('session'));

            switch ($user->getExtraData('queueType')) {
                case 'queue':
                    $this->sendText($user, $this->queue->getJsonQueue($session));
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
     * @param  User  $user
     * @param  mixed $command
     * @return null
     */
    private function gotAction(User $user, $command)
    {
        if (null == $user->getExtraData('session')) {
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
                $this->stopCollecting($command->id, isset($command->articles) ? $command->articles : null);
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
                break;
            case 'addArticle':
                $this->addArticle($user, $command->id, $command->articleId);
                break;
            case 'undoSale':
                $this->undoSale($user, $command->id);
                $this->sendQueueItemToAll($command->id);
                break;
        }
    }

    /**
     * @param  User     $user
     * @param  string   $universityIdentification
     * @return int|null
     */
    private function signIn(User $user, $universityIdentification)
    {
        $session = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOneById($user->getExtraData('session'));

        $item = $this->queue->addPerson($session, $universityIdentification);

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
                $this->entityManager,
                $this->entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.signin_printer'),
                $item,
                $this->entityManager
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllAssignedByPerson($item->getPerson())
            );

            return $item->getId();
        }
    }

    /**
     * @param  User     $user
     * @param  string   $universityIdentification
     * @return int|null
     */
    private function addToQueue(User $user, $universityIdentification)
    {
        $session = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOneById($user->getExtraData('session'));

        $item = $this->queue->addPerson($session, $universityIdentification, true);

        if (is_string($item)) {
            $object = (object) array(
                'addPersonError' => json_decode($item),
            );
            $this->sendText($user, json_encode($object));
        } else {
            return $item->getId();
        }
    }

    /**
     * @param  User    $user
     * @param  int     $id
     * @param  boolean $bulk
     * @return null
     */
    private function startCollecting(User $user, $id, $bulk = false)
    {
        $result = $this->queue->startCollecting($user, $id, $bulk);
        if ($result !== null) {
            $this->sendText($user, $result);
        }

        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        Printer::collectTicket(
            $this->entityManager,
            $user->getExtraData('paydesk'),
            $item,
            $this->entityManager
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findAllAssignedByPerson($item->getPerson())
        );
    }

    /**
     * @param  int        $id
     * @param  array|null $articles
     * @return null
     */
    private function stopCollecting($id, $articles = null)
    {
        $this->queue->stopCollecting($id, $articles);
    }

    /**
     * @param  int  $id
     * @return null
     */
    private function cancelCollecting($id)
    {
        $this->queue->cancelCollecting($id);
    }

    /**
     * @param  User $user
     * @param  int  $id
     * @return null
     */
    private function startSale(User $user, $id)
    {
        $this->sendText($user, $this->queue->startSale($user, $id));
    }

    /**
     * @param  int  $id
     * @return null
     */
    private function cancelSale($id)
    {
        $this->queue->cancelSale($id);
    }

    /**
     * @param  User   $user
     * @param  int    $id
     * @param  array  $articles
     * @param  array  $discounts
     * @param  string $payMethod
     * @return null
     */
    private function concludeSale(User $user, $id, $articles, $discounts, $payMethod)
    {
        $saleItems = $this->queue->concludeSale($id, $articles, $discounts, $payMethod);

        if (null == $saleItems) {
            return;
        }

        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        Printer::saleTicket(
            $this->entityManager,
            $user->getExtraData('paydesk'),
            $item,
            $saleItems
        );

    }

    /**
     * @param  int  $id
     * @return null
     */
    private function hold($id)
    {
        $this->queue->setHold($id);
    }

    /**
     * @param  int  $id
     * @return null
     */
    private function unhold($id)
    {
        $this->queue->setUnhold($id);
    }

    /**
     * @param  int    $id
     * @param  string $comment
     * @return null
     */
    private function saveComment($id, $comment)
    {
        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setComment($comment);
        $this->entityManager->flush();
    }

    /**
     * @param  User $user
     * @param  int  $id
     * @param  int  $articleId
     * @return null
     */
    private function addArticle(User $user, $id, $articleId)
    {
        $result = $this->queue->addArticle($id, $articleId);
        $this->sendText($user, $result);
    }

    /**
     * @param  User $user
     * @param  int  $id
     * @return null
     */
    private function undoSale(User $user, $id)
    {
        $this->queue->undoSale($id);

        $lightVersion = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.sale_light_version');

        if ($lightVersion == '1') {
            $this->startSale($user, $id);
        }
    }
}
