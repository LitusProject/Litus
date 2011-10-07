<?php

namespace Litus\Entity\Users;

use \Litus\Application\Resource\Doctrine as DoctrineResource;
use \Zend\Registry;

/**
 * @Entity(repositoryClass="Litus\Repository\Users\Session")
 * @Table(name="users.sessions")
 */
class Session
{
    /**
     * @var string The session ID
     *
     * @Id
     * @Column(type="string", length=32)
     */
    private $id;

    /**
     * @var \Datetime The time at which this session was started
     *
     * @Column(name="start_time", type="datetime")
     */
    private $startTime = null;

    /**
     * @var \Datetime The time at which this session will end
     *
     * @Column(name="expiration_time", type="datetime")
     */
    private $expirationTime = null;

    /**
     * @var \Litus\Entity\Users\Person The person associated with this session
     *
     * @ManyToOne(targetEntity="Litus\Entity\Users\Person", fetch="EAGER")
     * @JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var string The user agent used when the session was started
     *
     * @Column(name="user_agent", type="text")
     */
    private $userAgent;

    /**
     * @var string The IP address used when the session was started
     *
     * @Column(type="string", length=45)
     */
    private $ip;

    /**
     * @var bool Whether or not the user is logged in
     *
     * @Column(type="boolean")
     */
    private $active = true;

    /**
     * @param int $sessionExpire The duration of the session
     * @param \Litus\Entity\Users\Person $person The person associated with this session
     * @param string $userAgent The user agent used when the session was started
     * @param $ip The IP address used when the session was started
     */
    public function __construct($sessionExpire, Person $person, $userAgent, $ip)
    {
        $this->id = md5(uniqid(rand(), true));

        $this->startTime = new \Datetime();
        $this->expirationTime = new \Datetime(
            'now ' . (($sessionExpire < 0) ? '-' : '+') . abs($sessionExpire) . ' seconds'
        );

        $this->person = $person;
        $this->userAgent = $userAgent;
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Datetime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return \Datetime
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    /**
     * @return \Litus\Entity\Users\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return void
     */
    public function deactivate()
    {
        $this->active = false;
    }

    /**
     * Checks whether or not this session is valid.
     *
     * Note:
     * We don't delete expired sessions here, but wait for the garbage collector to clean up all expired sessions
     * at once.
     *
     * @param string $userAgent The user agent that should be checked
     * @param string $ip The IP currently used to connect to the site
     * @return bool|string
     */
    public function validateSession($userAgent, $ip)
    {
        if ($userAgent != $this->userAgent) {
            return false;
        }

        $now = new \Datetime();
        if ($this->expirationTime < $now) {
            return false;
        }

        if ($ip != $this->ip) {
            $newSession = new Session(
                $this->expirationTime,
                $this->person,
                $this->userAgent,
                $ip
            );

            $entityManager = Registry::get(DoctrineResource::REGISTRY_KEY);
            $entityManager->persist($newSession);

            return $newSession->getId();
        }

        return true;
    }
}
