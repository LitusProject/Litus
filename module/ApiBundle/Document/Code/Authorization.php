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
    CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ODM\MongoDB\Mapping\Annotations as ODM,
    Doctrine\ORM\EntityManager;

/**
 * This entity represents an authorization code used in OAuth 2.0.
 *
 * @ODM\Document(
 *     collection="apibundle_code_authorization",
 *     repositoryClass="ApiBundle\Repository\Code\Authorization"
 * )
 */
class Authorization
{
    const DEFAULT_EXPIRATION_TIME = 300;

    /**
     * @var string The ID of this authorization code
     *
     * @ODM\Id
     */
    private $id;

    /**
     * @var string The timestamp of entry
     *
     * @ODM\Field(type="string")
     */
    private $code;

    /**
     * @var integer The person that authorized the code
     *
     * @ODM\Field(type="int")
     */
    private $person;

    /**
     * @var integer The API key that was used to request the code
     *
     * @ODM\Field(type="int")
     */
    private $key;

    /**
     * @var \DateTime The expiration time of the code
     *
     * @ODM\Field(name="expiration_time", type="date")
     */
    private $expirationTime;

    /**
     * @var \DateTime The exchange time of the code
     *
     * @ODM\Field(name="exchange_time", type="date")
     */
    private $exchangeTime;

    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param \ApiBundle\Entity\Key            $key
     * @param int                              $expirationTime
     */
    public function __construct(Person $person, Key $key, $expirationTime = self::DEFAULT_EXPIRATION_TIME)
    {
        $this->code = bin2hex(openssl_random_pseudo_bytes(16));

        $this->person = $person->getId();
        $this->key = $key->getId();
        $this->expirationTime = new DateTime(
            'now ' . (($expirationTime < 0) ? '-' : '+') . abs($expirationTime) . ' seconds'
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
     * @param  \Doctrine\ORM\EntityManager      $entityManager
     * @return \CommonBundle\Entity\User\Person
     */
    public function getPerson(EntityManager $entityManager)
    {
        return $entityManager->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($this->person);
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
    public function getExpirationTime()
    {
        return $this->expirationTime;
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
     * Whether this authorization code has already been exchanged.
     *
     * @return boolean
     */
    public function hasBeenExchanged()
    {
        return null !== $this->exchangeTime;
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
