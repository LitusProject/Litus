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
use Ko\ProcessManager;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use React\EventLoop\LoopInterface;
use SplObjectStorage;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Socket
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class Socket implements MessageComponentInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    use ConfigTrait;
    use MailTransportTrait;
    use SentryClientTrait;

    use DoctrineTrait {
        getEntityManager as traitGetEntityManager;
    }

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var Command
     */
    private $command;

    /**
     * @var ProcessManager
     */
    private $processManager;

    /**
     * @var SplObjectStorage
     */
    private $users;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param LoopInterface           $loop
     * @param Command                 $command
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, LoopInterface $loop, Command $command)
    {
        $this->setServiceLocator($serviceLocator);

        $this->loop = $loop;
        $this->command = $command;
        $this->processManager = new ProcessManager();
        $this->users = new SplObjectStorage();

        $this->loop->addSignal(
            SIGTERM,
            function ($signal) {
                $this->onSignal($signal);
            }
        );
    }

    /**
     * @param  LoopInterface $loop
     * @return self
     */
    public function setLoop(LoopInterface $loop)
    {
        $this->loop = $loop;

        return $this;
    }

    /**
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
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
     * @return Command
     */
    protected function getCommand()
    {
        return $this->command;
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
     * @return ProcessManager
     */
    protected function getProcessManager()
    {
        return $this->processManager;
    }

    /**
     * @return SplObjectStorage
     */
    protected function getUsers()
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
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        $entityManager = $this->traitGetEntityManager();
        if (!$entityManager->getConnection()->ping()) {
            $entityManager->getConnection()->close();
            $entityManager->getConnection()->connect();
        }

        return $entityManager;
    }

    /**
     * @param integer $signal
     */
    protected function onSignal($signal)
    {
        switch ($signal) {
            case SIGTERM:
                foreach ($this->users as $user) {
                    $user->close();
                }

                $this->processManager->handleSigTerm();
                $this->loop->stop();

                break;

            default:
                $this->writeln('Received invalid signal <info>' . $signal . '</info>');
        }
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
    public function onError(ConnectionInterface $conn, Exception $e)
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
