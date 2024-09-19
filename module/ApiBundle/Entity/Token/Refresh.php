<?php

namespace ApiBundle\Entity\Token;

use ApiBundle\Entity\Code\Authorization as AuthorizationCode;
use ApiBundle\Entity\Key;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents an authorization code used in OAuth 2.0.
 *
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\Token\Refresh")
 * @ORM\Table(name="api_tokens_refresh")
 */
class Refresh extends \ApiBundle\Entity\Token
{
    const DEFAULT_EXPIRATION_TIME = 604800; // 1 week

    /**
     * @var Key The API key that can refresh the access token
     *
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Key")
     * @ORM\JoinColumn(name="key", referencedColumnName="id")
     */
    private $key;

    /**
     * @var DateTime The exchange time of the code
     *
     * @ORM\Column(name="exchange_time", type="datetime", nullable=true)
     */
    private $exchangeTime;

    /**
     * @param Person            $person
     * @param AuthorizationCode $authorizationCode
     * @param Key               $key
     * @param integer           $expirationTime
     */
    public function __construct(Person $person, AuthorizationCode $authorizationCode, Key $key, $expirationTime = self::DEFAULT_EXPIRATION_TIME)
    {
        parent::__construct($person, $authorizationCode, $expirationTime);

        $this->key = $key;
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
     * Whether this refresh token has already been exchanged.
     *
     * @return boolean
     */
    public function hasBeenExchanged()
    {
        return $this->exchangeTime !== null;
    }
}
