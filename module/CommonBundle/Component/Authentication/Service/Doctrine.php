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
    Zend\Authentication\Adapter\AdapterInterface,
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
    private $_entityManager = null;

    /**
     * @var string The name of the entity that holds the sessions
     */
    private $_entityName = '';

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
    )
    {
        parent::__construct($storage, $namespace, $cookieSuffix, $expire, $action);

        $this->_entityManager = $entityManager;

        if ('\\' == substr($entityName, 0, 1)) {
            throw new Exception\InvalidArgumentException(
                'The entity name cannot have a leading backslash'
            );
        }
        $this->_entityName = $entityName;
    }

    /**
     * Authenticates against the supplied adapter
     *
     * @param \CommonBundle\Component\Authentication\Adapter\Doctrine|null $adapter
     * @param boolean                                                      $rememberMe Remember this authentication session
     * @param boolean                                                      $shibboleth Whether or not this is sessions initiated by Shibboleth
     *
     * @return Result
     */
    public function authenticate(DoctrineAdapter $adapter = null, $rememberMe = false, $shibboleth = false)
    {
        $result = null;
        $server = $this->_server;

        if ('' == $this->getIdentity() && null !== $adapter) {
            $adapterResult = $adapter->authenticate();

            if ($adapterResult->isValid()) {
                $sessionEntity = $this->_entityName;
                $newSession = new $sessionEntity(
                    $adapterResult->getPersonObject(),
                    $server['HTTP_USER_AGENT'],
                    isset($server['HTTP_X_FORWARDED_FOR']) ? $server['HTTP_X_FORWARDED_FOR'] : $server['REMOTE_ADDR'],
                    $shibboleth,
                    $this->_duration
                );
                $this->_entityManager->persist($newSession);

                $adapterResult->setSessionObject($newSession);

                $this->getStorage()->write($newSession->getId());
                if ($rememberMe) {
                    $this->_setCookie($newSession->getId());
                } else {
                    $this->_clearCookie();
                }

                $result = $adapterResult;

                if (isset($this->_action))
                    $this->_action->succeededAction($result);
            } else {
                $result = $adapterResult;
                if (isset($this->_action))
                    $this->_action->failedAction($adapterResult);
            }
        } else {
            $session = $this->_entityManager->getRepository($this->_entityName)->findOneById(
                $this->getIdentity()
            );

            if (null !== $session) {
                $sessionValidation = $session->validate(
                    $this->_entityManager,
                    $server['HTTP_USER_AGENT'],
                    isset($server['HTTP_X_FORWARDED_FOR']) ? $server['HTTP_X_FORWARDED_FOR'] : $server['REMOTE_ADDR']
                );

                if (true !== $sessionValidation) {
                    $this->getStorage()->write($sessionValidation);

                    if ($this->_hasCookie() || $rememberMe) {
                        $this->_setCookie($sessionValidation);
                    } else {
                        $this->_clearCookie();
                    }
                }

                if (false !== $sessionValidation) {
                    $result = new Result(
                        Result::SUCCESS,
                        $session->getPerson()->getUsername(),
                        array(
                             'Authentication successful'
                        ),
                        $session->getPerson(),
                        $session
                    );
                }
            } else {
                $this->clearIdentity();
            }
        }

        $this->_entityManager->flush();

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
        if (!$this->hasIdentity())
            return;

        $session = $this->_entityManager->getRepository($this->_entityName)->findOneById(
            $this->getIdentity()
        );

        if (null !== $session) {
            $session->deactivate();
            $this->_entityManager->flush();
        }

        $this->getStorage()->clear();
        $this->_clearCookie();

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
            if ($this->_hasCookie())
                $this->getStorage()->write($this->_getCookie());
        }

        return !$this->getStorage()->isEmpty();
    }
}
