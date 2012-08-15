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
 * This entity stores a user's credentials.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\Users\Credential")
 * @Table(name="users.credentials")
 */
class Credential
{
    /**
     * @var int The ID of this credential
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var string The algorithm used to create the hash
     *
     * @Column(type="string", length=50)
     */
    private $algorithm;

    /**
     * @var string The salt used to create the hash
     *
     * @Column(type="string", length=32)
     */
    private $salt;

    /**
     * @var string The hashed credential, given by the user
     *
     * @Column(type="text")
     */
    private $hash;

    /**
     * Constructs a new credential
     *
     * @param string $algorithm The algorithm that should be used to create the hash
     * @param string $credential The credential that will be hashed and stored
     * @throws \InvalidArgumentException
     */
    public function __construct($algorithm, $credential)
    {
        if (!in_array($algorithm, hash_algos()))
            throw new \InvalidArgumentException('Invalid hash algorithm given: ' . $algorithm);

        $this->algorithm = $algorithm;
        $this->salt = md5(uniqid(rand(), true));

        $this->hash = hash_hmac(
            $algorithm, $credential, $this->salt
        );
    }

    /**
     * Checks whether or not the given credential is valid.
     *
     * @param string $credential The credential that should be checked
     * @return bool
     */
    public function validateCredential($credential)
    {
        return $this->hash == hash_hmac($this->algorithm, $credential, $this->salt);
    }
}
