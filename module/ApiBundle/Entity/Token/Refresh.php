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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

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
    const DEFAULT_EXPIRATION_TIME = 1209600;

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
