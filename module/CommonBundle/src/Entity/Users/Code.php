<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CommonBundle\Entity\Users;

/**
 * This entity stores a user's codes.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\Users\Code")
 * @Table(name="users.code")
 */
class Code
{
    /**
     * @var integer The ID of this code
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var \DateTime The expire time of this code
     *
     * @Column(name="expire_time", type="datetime", nullable=true)
     */
    private $expireTime;

    /**
     * @var string The code
     *
     * @Column(type="text", length=32, unique=true)
     */
    private $code;

    /**
     * Constructs a new code
     *
     * @param string $code
     * @param \DateTime|null $expireTime
     */
    public function __construct($code, $expireTime = null)
    {
        $this->code = $code;
        $this->expireTime = $expireTime;
    }
    
    /**
     * @return \DateTime
     */
    public function getExpireTime()
    {
        return $this->expireTime;
    }
    
    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
