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

namespace ApiBundle\Document;

use ApiBundle\Document\Code\Authorization as AuthorizationCode,
    CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ODM\MongoDB\Mapping\Annotations as ODM,
    Doctrine\ORM\EntityManager;

/**
 * This entity represents an access token used in OAuth 2.0.
 *
 * @ODM\MappedSuperclass
 * @ODM\InheritanceType("COLLECTION_PER_CLASS")
 */
abstract class Token
{
    const DEFAULT_EXPIRATION_TIME = 604800;

    /**
     * @var string The ID of this authorization code
     *
     * @ODM\Id
     */
    private $id;

    /**
     * @var string The token's code
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
     * @var \ApiBundle\Document\Code\Authorization The authorization code that was used to request the token
     *
     * @ODM\ReferenceOne(
     *     name="authorization_code",
     *     targetDocument="ApiBundle\Document\Code\Authorization",
     *     simple=true,
     *     cascade="persist"
     * )
     */
    private $authorizationCode;

    /**
     * @var \DateTime The expiration time of the code
     *
     * @ODM\Field(name="expiration_time", type="date")
     */
    private $expirationTime;

    /**
     * @param \CommonBundle\Entity\User\Person       $person
     * @param \ApiBundle\Document\Code\Authorization $authorizationCode
     * @param int                                    $expirationTime
     */
    public function __construct(Person $person, AuthorizationCode $authorizationCode, $expirationTime = self::DEFAULT_EXPIRATION_TIME)
    {
        $this->code = bin2hex(openssl_random_pseudo_bytes(16));

        $this->person = $person->getId();
        $this->authorizationCode = $authorizationCode;
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
     * @return \ApiBundle\Document\Code\Authorization
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
