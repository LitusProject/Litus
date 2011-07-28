<?php
namespace Litus\Entity\Users;

use \InvalidArgumentException;

/**
 * @Entity(repositoryClass="Litus\Repository\Users\Credential")
 * @Table(name="users.credentials")
 */
class Credential
{
    /**
     * The ID of this Credential.
     *
     * @var int
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * The algorithm used to create the hash.
     *
     * @var string
     *
     * @Column(type="string", length="50")
     */
    private $algorithm;

    /**
     * The salt used to create the hash.
     *
     * @var string
     *
     * @Column(type="string", length="32")
     */
    private $salt;

    /**
     * The hashed credential, given by the user.
     *
     * @var string
     *
     * @Column(type="text")
     */
    private $hash;

    /**
     * Constructs a new credential
     *
     * @throws \InvalidArgumentException
     * @param string $algorithm The algorithm that should be used to create the hash
     * @param string $credential The credential that will be hashed and stored
     */
    public function __construct($algorithm, $credential)
    {
        if (!in_array($algorithm, hash_algos()))
            throw new InvalidArgumentException('Invalid hash algorithm given: ' . $algorithm);
        $this->algorithm = $algorithm;
        $this->salt = md5(uniqid(rand(), true));
        $this->hash = hash($algorithm, $credential);
    }

    /**
     * Returns an encrypted version of the hash, using the generated salt.
     *
     * @return string
     */
    public function getCredential()
    {
        return crypt($this->hash, $this->salt);
    }

    /**
     * Checks whether or not the given credential is valid.
     *
     * @param string $credential The credential that should be checked
     * @return bool
     */
    public function validateCredential($credential)
    {
        return $credential == $this->getHash();
    }
}