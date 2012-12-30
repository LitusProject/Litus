<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShiftBundle\Document;

use CommonBundle\Entity\Users\Person,
    Doctrine\ODM\MongoDB\Mapping\Annotations as ODM,
    Doctrine\ORM\EntityManager;

/**
 * This entity stores a token used to generate a vCalendar so that we can create
 * an iCal file even when nobody is logged in.
 *
 * @ODM\Document(
 *     collection="shiftbundle_tokens",
 *     repositoryClass="ShiftBundle\Repository\Token"
 * )
 */
class Token
{
    /**
     * @var integer The ID of this token
     *
     * @ODM\Id
     */
    private $id;

    /**
     * @var string The token's hash
     *
     * @ODM\Field(type="string")
     * @ODM\UniqueIndex
     */
    private $hash;

    /**
     * @var string The person associated with this token
     *
     * @ODM\Field(type="int")
     */
    private $person;

    /**
     * @param \CommonBundle\Entity\Users\Person $person
     */
    public function __construct(Person $person)
    {
        $this->hash = md5(uniqid(rand(), true));
        $this->person = $person->getId();
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
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getPerson(EntityManager $entityManager)
    {
        return $entityManager->getRepository('CommonBundle\Entity\Users\Person')
            ->findOneById($this->person);
    }
}
