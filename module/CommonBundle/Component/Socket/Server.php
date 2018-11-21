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

use CommonBundle\Component\Console\Command;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\ConfigTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\DoctrineTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\MailTransportTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\SentryClientTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class Server implements MessageComponentInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    use ConfigTrait;
    use DoctrineTrait;
    use MailTransportTrait;
    use SentryClientTrait;

    /**
     * @var Command
     */
    private $command;

    /**
     * @var SplObjectStorage
     */
    private $users;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param Command                 $command
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, Command $command)
    {
        $this->setServiceLocator($serviceLocator);

        $this->command = $command;
        $this->users = new SplObjectStorage();
    }

    /**
     * @param  Command $command
     * @return self
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @param  string  $string
     * @param  boolean $raw
     * @return void
     */
    protected function write($string, $raw = false)
    {
        if ($this->command !== null) {
            $this->command->write($string, $raw);
        }
    }

    /**
     * @param  string  $string
     * @param  boolean $raw
     * @return void
     */
    protected function writeln($string, $raw = false)
    {
        if ($this->command !== null) {
            $this->command->writeln($string, $raw);
        }
    }

    /**
     * @return SplObjectStorage
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User $user
     */
    protected function addUser(User $user)
    {
        $this->users->attach($user);
    }

    /**
     * @param User $user
     */
    protected function removeUser(User $user)
    {
        $user->close();
        $this->users->detach($user);
    }

    /**
     * @param  ConnectionInterface $conn
     * @return User
     */
    protected function getUser(ConnectionInterface $conn)
    {
        foreach ($this->users as $user) {
            if ($user->getConnection() === $conn) {
                return $user;
            }
        }

        return null;
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->addUser(
            new User(
                $this->getEntityManager(),
                $conn
            )
        );
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->users->detach(
            $this->getUser($conn)
        );
    }

    /**
     * @param ConnectionInterface $conn
     * @param Exception           $e
     */
    // phpcs:disable SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly
    public function onError(ConnectionInterface $conn, Exception $e)
    // phpcs:enable
    {
        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getSentryClient()->logException($e);
        }

        $this->writeln('<error>' . $e->getMessage() . '</error>');
    }

    /**
     * @param ConnectionInterface $from
     * @param string              $msg
     */
    abstract public function onMessage(ConnectionInterface $from, $msg);
}
