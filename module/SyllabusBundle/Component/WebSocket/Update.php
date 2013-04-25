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

namespace SyllabusBundle\Component\WebSocket\Syllabus;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Component\WebSocket\User,
    DateTime,
    Doctrine\ORM\EntityManager,
    SyllabusBundle\Component\XMLParser\Study as StudyParser,
    Zend\Mail\Transport\TransportInterface;

/**
 * This is the server to handle all requests by the websocket protocol for the Queue.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Update extends \CommonBundle\Component\WebSocket\Server
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @var \Zend\Mail\Transport\TransportInterface
     */
    private $_mailTransport;

    /**
     * @var string
     */
    private $_status = 'done';

    /**
     * @param Doctrine\ORM\EntityManager $entityManager
     * @param \Zend\Mail\Transport\TransportInterface $mailTransport
     */
    public function __construct(EntityManager $entityManager, TransportInterface $mailTransport)
    {
        $address = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.update_socket_host');
        $port = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.update_socket_port');

        parent::__construct($address, $port);

        $this->_entityManager = $entityManager;
        $this->_mailTransport = $mailTransport;
    }

    /**
     * Parse received text
     *
     * @param \CommonBundle\Component\WebSockets\Sale\User $user
     * @param string $data
     */
    protected function gotText(User $user, $data)
    {
        $command = json_decode($data);

        $key = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.queue_socket_key');

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
                        $acl, 'syllabus_admin_update', 'updateNow'
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


        $this->addAuthenticated($user->getSocket());

        if ($command->command == 'update' && 'done' == $this->_status) {
            $this->_entityManager->clear();
            $this->_status = 'updating';
            new StudyParser($this->_entityManager, $this->_mailTransport, 'http://litus/admin/syllabus/update/xml', array($this, 'callback'));
            $this->callback('done');
            $this->_status = 'done';
        }
    }

    /**
     * @param string $type
     * @param null|string $extra
     */
    public function callback($type, $extra = null)
    {
        $this->sendTextToAll(
            json_encode(
                (object) array(
                    'status' => (object) array(
                        'type' => $type,
                        'extra' => substr(trim($extra), 0, 74),
                    )
                )
            )
        );
    }
}
