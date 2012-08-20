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

namespace ApiBundle\Entity;

use DateTime;

/**
 * This entity stores a user's codes.
 *
 * @Entity(repositoryClass="ApiBundle\Repository\Key")
 * @Table(name="api.key")
 */
class Key
{
    /**
     * @var integer The ID of this code
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var \DateTime The expire time of this code
     *
     * @Column(name="expiration_time", type="datetime", nullable=true)
     */
    private $expirationTime;

    /**
     * @var string The host this key's valid for
     *
     * @Column(type="string")
     */
    private $host;

    /**
     * @var string The code
     *
     * @Column(type="string", length=32, unique=true)
     */
    private $code;

    /**
     * @param string $host
     * @param string $code
     * @param int $expirationTime
     */
    public function __construct($host, $code, $expirationTime = 946080000)
    {
        $this->expirationTime = new DateTime(
            'now ' . (($expirationTime < 0) ? '-' : '+') . abs($expirationTime) . ' seconds'
        );

        $this->host = $host;
        $this->code = $code;
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
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return \ApiBundle\Entity\Key
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Revokes the API key.
     *
     * @return void
     */
    public function revoke()
    {
        $this->expirationTime = new DateTime();
    }

    /**
     * Checks whether or not this key is valid.
     *
     * @param string $ip The remote IP
     * @return boolean
     */
    public function vaidate($ip)
    {
        $now = new DateTime();
        if ($this->expirationTime < $now) {
            return false;
        }

        //TODO: Check the host address

        return true;
    }
}
