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

use ApiBundle\Document\Code\Authorization,
    ApiBundle\Entity\Key,
    CommonBundle\Entity\User\Person\Academic,
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
    /**
     * @var integer The ID of this authorization code
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
     * @var integer The academic that authorized the code
     *
     * @ODM\Field(type="int")
     */
    private $academic;

    /**
     * @var \ApiBundle\Document\Code\Authorization The authorization code that was used to request the token
     *
     * @ReferenceOne(
     *     targetDocument="ApiBundle\Document\Code\Authorization",
     *     cascade="persist"
     * )
     */
    private $authorizationCode;

    /**
     * @var integer The expiration time of the code
     *
     * @ODM\Field(type="date")
     */
    private $expirationTime;

    /**
     * @param \CommonBundle\Entity\User\Person\Academic $academic
     * @param \ApiBundle\Document\Code\Authorization $authorizationCode
     * @param int $expirationTime
     */
    public function __construct(Academic $academic, Authorization $authorizationCode, $expirationTime = 604800)
    {
        $this->code = bin2hex(openssl_random_pseudo_bytes(16));

        $this->academic = $academic->getId();
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
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @return \CommonBundle\Entity\User\Person\Academic
     */
    public function getAcademic(EntityManager $entityManager)
    {
        return $entityManager->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($this->academic);
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
}
