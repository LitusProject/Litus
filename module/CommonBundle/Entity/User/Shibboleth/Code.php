<?php

namespace CommonBundle\Entity\User\Shibboleth;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * We register the server's hostname as the Shibboleth SP with the KU Leuven.
 * Because of this, however, we need to create an extra step to get the authentication
 * result to Litus.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Shibboleth\Code")
 * @ORM\Table(name="users_shibboleth_codes")
 */
class Code
{
    /**
     * @var string The ID of this code
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=32)
     */
    private $id;

    /**
     * @var DateTime The time at which this code was created
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var DateTime The time at which this session will end
     *
     * @ORM\Column(name="expiration_time", type="datetime")
     */
    private $expirationTime;

    /**
     * @var string The authenticated person's university identification
     *
     * @ORM\Column(name="university_identification", type="string", length=8)
     */
    private $universityIdentification;

    /**
     * @var string The code
     *
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $code;

    /**
     * @var string The additional information
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $info;

    /**
     * @var string The redirect url
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $redirect;

    /**
     * @param string      $universityIdentification
     * @param string      $code
     * @param string      $info
     * @param integer     $expirationTime
     * @param string|null $redirect
     */
    public function __construct($universityIdentification, $code, $info, $expirationTime = 300, $redirect = null)
    {
        $this->id = md5(uniqid(rand(), true));

        $this->creationTime = new DateTime();
        $this->expirationTime = new DateTime(
            'now ' . ($expirationTime < 0 ? '-' : '+') . abs($expirationTime) . ' seconds'
        );

        $this->universityIdentification = $universityIdentification;
        $this->code = $code;
        $this->info = $info;
        $this->redirect = $redirect;
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
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return DateTime
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    /**
     * @return string
     */
    public function getUniversityIdentification()
    {
        return $this->universityIdentification;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return string
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * Generates a hash from our code.
     *
     * @return string
     */
    public function hash()
    {
        return sha1($this->code);
    }

    /**
     * Checks whether or not this code is valid.
     *
     * Note:
     * We don't delete expired codes here, but wait for the garbage collector to clean
     * up all expired sessions at once.
     *
     * @param  string $hash The hash that was received
     * @return boolean
     */
    public function validate($hash)
    {
        $now = new DateTime();
        if ($this->expirationTime < $now) {
            return false;
        }

        return $hash == sha1($this->code);
    }
}
