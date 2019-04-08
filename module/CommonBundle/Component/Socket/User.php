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

namespace CommonBundle\Component\Socket;

use CommonBundle\Component\Acl\Acl;
use Doctrine\ORM\EntityManager;
use Ratchet\ConnectionInterface;

class User implements ConnectionInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    private $id;

    /**
     * @var \Ratchet\ConnectionInterface
     */
    private $connection;

    /**
     * @var \CommonBundle\Entity\User\Session
     */
    private $authSession;

    /**
     * @var array
     */
    private $data;

    /**
     * @param EntityManager       $entityManager
     * @param ConnectionInterface $conn
     */
    public function __construct(EntityManager $entityManager, ConnectionInterface $conn)
    {
        $this->entityManager = $entityManager;

        $this->id = uniqid();
        $this->connection = $conn;
        $this->authSession = null;
        $this->data = array();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  ConnectionInterface $connection
     * @return self
     */
    public function setConnection(ConnectionInterface $conn)
    {
        $this->connection = $conn;

        return $this;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param  string $data
     * @return self
     */
    public function send($data)
    {
        $this->connection->send($data);

        return $this;
    }

    /**
     * @return self
     */
    public function close()
    {
        $this->connection->close();

        return $this;
    }

    /**
     * @param  string $authSession
     * @return boolean
     */
    public function authenticate($authSession)
    {
        if (getenv('APPLICATION_ENV') == 'development') {
            return true;
        }

        $authSession = $this->entityManager
            ->getRepository('CommonBundle\Entity\User\Session')
            ->findOneById($authSession);

        if ($authSession !== null) {
            $this->authSession = $authSession;

            return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function isAuthenticated()
    {
        if (getenv('APPLICATION_ENV') == 'development') {
            return true;
        }

        return $this->authSession !== null;
    }

    /**
     * @param  string $resource
     * @param  string $action
     * @return boolean
     */
    public function isAllowed($resource, $action)
    {
        if (getenv('APPLICATION_ENV') == 'development') {
            return true;
        }

        $isAllowed = false;
        if ($this->authSession !== null) {
            $acl = new Acl($this->entityManager);

            foreach ($this->authSession->getPerson()->getRoles() as $role) {
                if ($role->isAllowed($acl, $resource, $action)) {
                    $isAllowed = true;
                    break;
                }
            }
        }

        return $isAllowed;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->data)) {
            return null;
        }

        return $this->data[$name];
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }
}
