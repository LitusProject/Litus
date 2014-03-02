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

namespace CommonBundle\Entity\User;

use DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * We store all sessions in the database, so that we have a tidbit more information and
 * the authentication process can be made slightly more secure.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Session")
 * @ORM\Table(name="users.sessions")
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
     * @var \DateTime The time at which this session was started
     *
     * @ORM\Column(name="start_time", type="datetime")
     */
    private $startTime;

    /**
     * @var \DateTime The time at which this session will end
     *
     * @ORM\Column(name="expiration_time", type="datetime")
     */
    private $expirationTime;

    /**
     * @var \CommonBundle\Entity\User\Person The person associated with this session
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
     * @var bool Whether or not the user is logged in
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var string The type of this session
     *
     * @ORM\Column(type="boolean")
     */
    private $shibboleth;

    /**
     * @param int|\DateTime                    $expirationTime
     * @param \CommonBundle\Entity\User\Person $person
     * @param string                           $userAgent
     * @param string                           $ip
     */
    public function __construct(Person $person, $userAgent, $ip, $shibboleth, $expirationTime = 3600)
    {
        $this->id = md5(uniqid(rand(), true));

        $this->startTime = new DateTime();

        if (is_int($expirationTime)) {
            $this->expirationTime = new DateTime(
                'now ' . (($expirationTime < 0) ? '-' : '+') . abs($expirationTime) . ' seconds'
            );
        } else {
            $this->expirationTime = $expirationTime;
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
     * @return \CommonBundle\Entity\User\Person
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
     * @param  \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param  string                      $userAgent     The user agent that should be checked
     * @param  string                      $ip            The IP currently used to connect to the site
     * @return bool|string
     */
    public function validate(EntityManager $entityManager, $userAgent, $ip)
    {
        if ($userAgent != $this->userAgent || !$this->active)
            return false;

        $now = new DateTime();
        if ($this->expirationTime < $now)
            return false;

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
