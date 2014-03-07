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

namespace ApiBundle\Document\Code;

use ApiBundle\Entity\Key,
    CommonBundle\Entity\User\Person\Academic,
    DateTime,
    Doctrine\ODM\MongoDB\Mapping\Annotations as ODM,
    Doctrine\ORM\EntityManager;

/**
 * This entity represents an authorization code used in OAuth 2.0.
 *
 * @ODM\Document(
 *     collection="apibundle_token_refresh",
 *     repositoryClass="ApiBundle\Repository\Code\Authorization"
 * )
 */
class Refresh extends \ApiBundle\Document\Token
{
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
     * @param \CommonBundle\Entity\User\Person\Academic $academic
     * @param \ApiBundle\Document\Code\Authorization $authorizationCode
     * @param \ApiBundle\Entity\Key $key
     * @param int $expirationTime
     */
    public function __construct(Academic $academic, Authorization $authorizationCode, Key $key, $expirationTime = 1209600)
    {
        parent::__construct($academic, $authorizationCode, $expirationTime);

        $this->key = $key->getId();
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
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
}
