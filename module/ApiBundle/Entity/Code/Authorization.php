<?php

namespace ApiBundle\Entity\Code;

use ApiBundle\Entity\Key;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents an authorization code used in OAuth 2.0.
 *
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\Code\Authorization")
 * @ORM\Table(name="api_codes_authorization")
 */
class Authorization
{
    const DEFAULT_EXPIRATION_TIME = 300;

    /**
     * @var string The ID of this authorization code
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The authorization code
     *
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @var Person The person that authorized the code
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var Key The API key that was used to request the code
     *
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Key")
     * @ORM\JoinColumn(name="key", referencedColumnName="id")
     */
    private $key;

    /**
     * @var DateTime The expiration time of the code
     *
     * @ORM\Column(name="expiration_time", type="datetime")
     */
    private $expirationTime;

    /**
     * @var DateTime The exchange time of the code
     *
     * @ORM\Column(name="exchange_time", type="datetime", nullable=true)
     */
    private $exchangeTime;

    /**
     * @param Person  $person
     * @param Key     $key
     * @param integer $expirationTime
     */
    public function __construct(Person $person, Key $key, $expirationTime = self::DEFAULT_EXPIRATION_TIME)
    {
        $this->code = bin2hex(openssl_random_pseudo_bytes(16));

        $this->person = $person;
        $this->key = $key;
        $this->expirationTime = new DateTime(
            'now ' . ($expirationTime < 0 ? '-' : '+') . abs($expirationTime) . ' seconds'
        );
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return Key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return DateTime
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    /**
     * @return DateTime
     */
    public function getExchangeTime()
    {
        return $this->exchangeTime;
    }

    /**
     * @return self
     */
    public function exchange()
    {
        $this->exchangeTime = new DateTime();

        return $this;
    }

    /**
     * Whether this authorization code has already been exchanged.
     *
     * @return boolean
     */
    public function hasBeenExchanged()
    {
        return $this->exchangeTime !== null;
    }

    /**
     * Whether this authorization code has expired.
     *
     * @return boolean
     */
    public function hasExpired()
    {
        return $this->expirationTime < new DateTime();
    }
}
