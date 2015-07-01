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

namespace CommonBundle\Component\Authentication\Service;

use CommonBundle\Component\Authentication\Action,
    CommonBundle\Component\Authentication\Adapter\Doctrine as DoctrineAdapter,
    CommonBundle\Component\Authentication\Result\Doctrine as Result,
    Doctrine\ORM\EntityManager,
    Zend\Authentication\Storage\StorageInterface;

/**
 * An authentication service that uses a Doctrine result.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Doctrine extends \CommonBundle\Component\Authentication\AbstractAuthenticationService
{
    /**
     * @var EntityManager The EntityManager instance
     */
    private $entityManager = null;

    /**
     * @var string The name of the entity that holds the sessions
     */
    private $entityName = '';

    /**
     * @param  EntityManager                      $entityManager The EntityManager instance
     * @param  string                             $entityName    The name of the entity that holds the sessions
     * @param  int                                $expire        The expiration time for the persistent storage
     * @param  StorageInterface                   $storage       The persistent storage handler
     * @param  string                             $namespace     The namespace the storage handlers will use
     * @param  string                             $cookieSuffix  The cookie suffix that is used to store the session cookie
     * @param  Action                             $action        The action that should be taken after authentication
     * @throws Exception\InvalidArgumentException The entity name cannot have a leading backslash
     */
    public function __construct(
        EntityManager $entityManager, $entityName, $expire, StorageInterface $storage, $namespace, $cookieSuffix, Action $action
    ) {
        parent::__construct($storage, $namespace, $cookieSuffix, $expire, $action);

        $this->entityManager = $entityManager;

        if ('\\' == substr($entityName, 0, 1)) {
            throw new Exception\InvalidArgumentException(
                'The entity name cannot have a leading backslash'
            );
        }
        $this->entityName = $entityName;
    }

    /**
     * Authenticates against the supplied adapter
     *
     * @param \CommonBundle\Component\Authentication\Adapter\Doctrine|null $adapter
     * @param boolean                                                      $rememberMe Remember this authentication session
     * @param boolean                                                      $shibboleth Whether or not this is sessions initiated by Shibboleth
     *
     * @return Result|null
     */
    public function authenticate(DoctrineAdapter $adapter = null, $rememberMe = false, $shibboleth = false)
    {
        $result = null;
        if (null == $this->request) {
            return;
        }

        $server = $this->request->getServer();

        if ('' == $this->getIdentity() && null !== $adapter) {
            $adapterResult = $adapter->authenticate();

            if ($adapterResult->isValid()) {
                $sessionEntity = $this->entityName;
                $newSession = new $sessionEntity(
                    $adapterResult->getPersonObject(),
                    $server->get('HTTP_USER_AGENT'),
                    $server->get('HTTP_X_FORWARDED_FOR', $server->get('REMOTE_ADDR')),
                    $shibboleth,
                    $this->duration
                );
                $this->entityManager->persist($newSession);

                $adapterResult->setSessionObject($newSession);

                $this->getStorage()->write($newSession->getId());
                if ($rememberMe) {
                    $this->setCookie($newSession->getId());
                } else {
                    $this->clearCookie();
                }

                $result = $adapterResult;

                if (isset($this->action)) {
                    $this->action->succeededAction($result);
                }
            } else {
                $result = $adapterResult;
                if (isset($this->action)) {
                    $this->action->failedAction($adapterResult);
                }
            }
        } else {
            $session = $this->entityManager->getRepository($this->entityName)->findOneById(
                $this->getIdentity()
            );

            if (null !== $session) {
                $sessionValidation = $session->validate(
                    $this->entityManager,
                    $server->get('HTTP_USER_AGENT'),
                    $server->get('HTTP_X_FORWARDED_FOR', $server->get('REMOTE_ADDR'))
                );

                if (true !== $sessionValidation) {
                    $this->getStorage()->write($sessionValidation);

                    if ($this->hasCookie() || $rememberMe) {
                        $this->setCookie($sessionValidation);
                    } else {
                        $this->clearCookie();
                    }
                }

                if (false !== $sessionValidation) {
                    $result = new Result(
                        Result::SUCCESS,
                        $session->getPerson()->getUsername(),
                        array(
                             'Authentication successful',
                        ),
                        $session->getPerson(),
                        $session
                    );
                }
            } else {
                $this->clearIdentity();
            }
        }

        $this->entityManager->flush();

        return $result;
    }

    /**
     * Returns the session identity.
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->hasIdentity() ? $this->getStorage()->read() : '';
    }

    /**
     * Clears the persistent storage and deactivates the associated session.
     *
     * @return \CommonBundle\Entity\User\Session|null
     */
    public function clearIdentity()
    {
        if (!$this->hasIdentity()) {
            return;
        }

        $session = $this->entityManager->getRepository($this->entityName)->findOneById(
            $this->getIdentity()
        );

        if (null !== $session) {
            $session->deactivate();
            $this->entityManager->flush();
        }

        $this->getStorage()->clear();
        $this->clearCookie();

        return $session;
    }

    /**
     * Checks whether or not there is a stored session identity.
     *
     * @return bool
     */
    public function hasIdentity()
    {
        if ($this->getStorage()->isEmpty() || $this->getStorage()->read() == '') {
            if ($this->hasCookie()) {
                $this->getStorage()->write($this->getCookie());
            }
        }

        return !$this->getStorage()->isEmpty();
    }
}
