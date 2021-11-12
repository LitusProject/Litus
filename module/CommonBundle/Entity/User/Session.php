<?php

namespace CommonBundle\Entity\User;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * We store all sessions in the database, so that we have a tidbit more information and
 * the authentication process can be made slightly more secure.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Session")
 * @ORM\Table(name="users_sessions")
 */
class Session
{
    /**
     * @var string The session ID
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=32)
     */
    private $id;

    /**
     * @var DateTime The time at which this session was started
     *
     * @ORM\Column(name="start_time", type="datetime")
     */
    private $startTime;

    /**
     * @var DateTime The time at which this session will end
     *
     * @ORM\Column(name="expiration_time", type="datetime")
     */
    private $expirationTime;

    /**
     * @var Person The person associated with this session
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person", fetch="EAGER")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var string The user agent used when the session was started
     *
     * @ORM\Column(name="user_agent", type="text")
     */
    private $userAgent;

    /**
     * @var string The IP address used when the session was started
     *
     * @ORM\Column(type="string", length=45)
     */
    private $ip;

    /**
     * @var boolean Whether or not the user is logged in
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var boolean The type of this session
     *
     * @ORM\Column(type="boolean")
     */
    private $shibboleth;

    /**
     * @param Person           $person
     * @param string           $userAgent
     * @param string           $ip
     * @param boolean          $shibboleth
     * @param DateTime|integer $expirationTime
     */
    public function __construct(Person $person, $userAgent, $ip, $shibboleth, $expirationTime = 3600)
    {
        $this->id = md5(uniqid(rand(), true));

        $this->startTime = new DateTime();

        if ($expirationTime instanceof DateTime) {
            $this->expirationTime = $expirationTime;
        } else {
            $expirationTime = is_int($expirationTime) ? $expirationTime : 3600;
            $this->expirationTime = new DateTime(
                'now ' . ($expirationTime < 0 ? '-' : '+') . abs($expirationTime) . ' seconds'
            );
        }

        $this->person = $person;
        $this->userAgent = $userAgent;
        $this->ip = $ip;

        $this->shibboleth = $shibboleth;

        $this->active = true;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return DateTime
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    /**
     * @return Person
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
     * @return boolean
     */
    public function isShibboleth()
    {
        return $this->shibboleth;
    }

    /**
     * Checks whether or not this session is valid.
     *
     * Note:
     * We don't delete expired sessions here, but wait for the garbage collector to clean up all expired sessions
     * at once.
     *
     * @param  EntityManager $entityManager The EntityManager instance
     * @param  string        $userAgent     The user agent that should be checked
     * @param  string        $ip            The IP currently used to connect to the site
     * @return boolean|string
     */
    public function validate(EntityManager $entityManager, $userAgent, $ip)
    {
        if ($userAgent != $this->userAgent || !$this->active) {
            return false;
        }

        $now = new DateTime();
        if ($this->expirationTime < $now) {
            return false;
        }

        if ($ip != $this->ip) {
            $this->deactivate();

            $newSession = new Session(
                $this->person,
                $this->userAgent,
                $ip,
                $this->shibboleth,
                $this->expirationTime
            );

            $entityManager->persist($newSession);
            $entityManager->flush();

            return $newSession->getId();
        }

        return true;
    }
}
