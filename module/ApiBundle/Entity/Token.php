<?php

namespace ApiBundle\Entity;

use ApiBundle\Entity\Code\Authorization as AuthorizationCode;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents an access token used in OAuth 2.0.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Person")
 * @ORM\Table(name="api_tokens")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "access"="ApiBundle\Entity\Token\Access",
 *      "refresh"="ApiBundle\Entity\Token\Refresh"
 * })
 */
abstract class Token
{
    const DEFAULT_EXPIRATION_TIME = 3600; // 1 hour

    /**
     * @var string The ID of this authorization code
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The token's code
     *
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @var \CommonBundle\Entity\User\Person The person that authorized the code
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var \ApiBundle\Entity\Code\Authorization The authorization code that was used to request the token
     *
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Code\Authorization")
     * @ORM\JoinColumn(name="authorization_code", referencedColumnName="id")
     */
    private $authorizationCode;

    /**
     * @var \DateTime The expiration time of the code
     *
     * @ORM\Column(name="expiration_time", type="datetime")
     */
    private $expirationTime;

    /**
     * @param \CommonBundle\Entity\User\Person     $person
     * @param \ApiBundle\Entity\Code\Authorization $authorizationCode
     * @param integer                              $expirationTime
     */
    public function __construct(Person $person, AuthorizationCode $authorizationCode, $expirationTime = self::DEFAULT_EXPIRATION_TIME)
    {
        $this->code = bin2hex(openssl_random_pseudo_bytes(16));

        $this->person = $person;
        $this->authorizationCode = $authorizationCode;
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
     * @return \CommonBundle\Entity\User\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return \ApiBundle\Entity\Code\Authorization
     */
    public function getAuthorizationCode()
    {
        return $this->authorizationCode;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    /**
     * Whether this token has expired.
     *
     * @return boolean
     */
    public function hasExpired()
    {
        return $this->expirationTime < new DateTime();
    }
}
