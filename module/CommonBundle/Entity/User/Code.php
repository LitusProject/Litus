<?php

namespace CommonBundle\Entity\User;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a user's codes.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Code")
 * @ORM\Table(name="users_codes")
 */
class Code
{
    /**
     * @var integer The ID of this code
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The expire time of this code
     *
     * @ORM\Column(name="expiration_time", type="datetime", nullable=true)
     */
    private $expirationTime;

    /**
     * @var string The code
     *
     * @ORM\Column(type="string", length=32, unique=true)
     */
    private $code;

    /**
     * @param string  $code
     * @param integer $expirationTime
     */
    public function __construct($code, $expirationTime = 604800)
    {
        $this->expirationTime = new DateTime(
            'now ' . ($expirationTime < 0 ? '-' : '+') . abs($expirationTime) . ' seconds'
        );

        $this->code = $code;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
    public function getCode()
    {
        return $this->code;
    }
}
