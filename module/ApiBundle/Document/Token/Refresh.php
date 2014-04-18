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

namespace ApiBundle\Document\Token;

use ApiBundle\Document\Code\Authorization as AuthorizationCode,
    ApiBundle\Entity\Key,
    CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ODM\MongoDB\Mapping\Annotations as ODM,
    Doctrine\ORM\EntityManager;

/**
 * This entity represents an authorization code used in OAuth 2.0.
 *
 * @ODM\Document(
 *     collection="apibundle_token_refresh",
 *     repositoryClass="ApiBundle\Repository\Token\Refresh"
 * )
 */
class Refresh extends \ApiBundle\Document\Token
{
    const DEFAULT_EXPIRATION_TIME = 1209600;

    /**
     * @var integer The API key that can refresh the access token
     *
     * @ODM\Field(type="int")
     */
    private $key;

    /**
     * @var integer The exchange time of the code
     *
     * @ODM\Field(name="exchange_time", type="date")
     */
    private $exchangeTime;

    /**
     * @param \CommonBundle\Entity\User\Person       $person
     * @param \ApiBundle\Document\Code\Authorization $authorizationCode
     * @param \ApiBundle\Entity\Key                  $key
     * @param int                                    $expirationTime
     */
    public function __construct(Person $person, AuthorizationCode $authorizationCode, Key $key, $expirationTime = self::DEFAULT_EXPIRATION_TIME)
    {
        parent::__construct($person, $authorizationCode, $expirationTime);

        $this->key = $key->getId();
    }

    /**
     * @param  \Doctrine\ORM\EntityManager $entityManager
     * @return \ApiBundle\Entity\Key
     */
    public function getKey(EntityManager $entityManager)
    {
        return $entityManager->getRepository('ApiBundle\Entity\Key')
            ->findOneById($this->key);
    }

    /**
     * @return \DateTime
     */
    public function getExchangeTime()
    {
        return $this->exchangeTime;
    }

    /**
     * @return \ApiBundle\Document\Code\Authorization
     */
    public function exchange()
    {
        $this->exchangeTime = new \DateTime();

        return $this;
    }

    /**
     * Whether this refresh token has already been exchanged.
     *
     * @return boolean
     */
    public function hasBeenExchanged()
    {
        return null !== $this->exchangeTime;
    }
}
