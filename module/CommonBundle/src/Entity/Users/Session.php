<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CommonBundle\Entity\Users;

use Doctrine\ORM\EntityManager;

/**
 * We store all sessions in the database, so that we have a tidbit more information and
 * the authentication process can be made slightly more secure.
 * 
 * @Entity(repositoryClass="CommonBundle\Repository\Users\Session")
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
     * @var \DateTime The time at which this session was started
     *
     * @Column(name="start_time", type="DateTime")
     */
    private $startTime = null;

    /**
     * @var \DateTime The time at which this session will end
     *
     * @Column(name="expiration_time", type="DateTime")
     */
    private $expirationTime = null;

    /**
     * @var \CommonBundle\Entity\Users\Person The person associated with this session
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person", fetch="EAGER")
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
     * @param int $expirationTime
     * @param \CommonBundle\Entity\Users\Person $person
     * @param string $userAgent
     * @param string $ip
     */
    public function __construct($expirationTime, Person $person, $userAgent, $ip)
    {
        $this->id = md5(uniqid(rand(), true));

        $this->startTime = new \DateTime();
        $this->expirationTime = new \DateTime(
            'now ' . (($expirationTime < 0) ? '-' : '+') . abs($expirationTime) . ' seconds'
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
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
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
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string $userAgent The user agent that should be checked
     * @param string $ip The IP currently used to connect to the site
     * @return bool|string
     */
    public function validateSession(EntityManager $entityManager, $userAgent, $ip)
    {
        if ($userAgent != $this->userAgent) {
            return false;
        }

        $now = new \DateTime();
        if ($this->expirationTime < $now) {
            return false;
        }

        if ($ip != $this->ip) {
            $this->deactivate();

            $newSession = new Session(
                $this->expirationTime,
                $this->person,
                $this->userAgent,
                $ip
            );

            $entityManager->persist($newSession);
            $entityManager->flush();

            return $newSession->getId();
        }

        return true;
    }
}
