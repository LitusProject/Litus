<?php

namespace CommonBundle\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * This entity stores a user's credentials.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Credential")
 * @ORM\Table(name="users_credentials")
 */
class Credential
{
    const DEFAULT_ALGORITHM = 'sha512';
    const DEFAULT_NB_ITERATIONS = 1000;

    /**
     * @var integer The ID of this credential
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The algorithm used to create the hash
     *
     * @ORM\Column(type="string", length=50)
     */
    private $algorithm;

    /**
     * @var string The salt used to create the hash
     *
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @var string The hashed credential, given by the user
     *
     * @ORM\Column(type="text")
     */
    private $hash;

    /**
     * @var integer The number of hash iterations
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $iterations;

    /**
     * Constructs a new credential
     *
     * @param  string $algorithm  The algorithm that should be used to create the hash
     * @param  string $credential The credential that will be hashed and stored
     * @throws InvalidArgumentException
     */
    public function __construct($credential, $algorithm = self::DEFAULT_ALGORITHM, $iterations = self::DEFAULT_NB_ITERATIONS)
    {
        if (!in_array($algorithm, hash_algos())) {
            throw new InvalidArgumentException('Invalid hash algorithm given: ' . $algorithm);
        }

        $this->algorithm = $algorithm;
        $this->salt = bin2hex(openssl_random_pseudo_bytes(16));
        $this->iterations = $iterations;

        $this->hash = $this->hash($credential);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Hashes a credential.
     *
     * @param  string $credential The credential to hash
     * @return string the hashed credential
     */
    private function hash($credential)
    {
        $hash = hash_hmac($this->algorithm, $credential, $this->salt);
        for ($i = 0; $i < $this->iterations; $i++) {
            $hash = hash_hmac($this->algorithm, $hash, $this->salt);
        }

        return $hash;
    }

    /**
     * Checks whether or not the given credential is valid.
     *
     * @param  string $credential The credential that should be checked
     * @return boolean
     */
    public function validateCredential($credential)
    {
        return $this->hash($credential) == $this->hash;
    }

    /**
     * Checks whether the credential should be updated.
     *
     * @return boolean
     */
    public function shouldUpdate()
    {
        return ($this->algorithm !== self::DEFAULT_ALGORITHM) || ($this->iterations !== self::DEFAULT_NB_ITERATIONS);
    }

    /**
     * Updates the credential if needed.
     * If the given credential does not match, nothing happens.
     *
     * @param string $credential the unhashed credential to update
     */
    public function update($credential)
    {
        if (!$this->shouldUpdate() || !$this->validateCredential($credential)) {
            return;
        }

        $this->algorithm = self::DEFAULT_ALGORITHM;
        $this->iterations = self::DEFAULT_NB_ITERATIONS;
        $this->hash = $this->hash($credential);
    }
}
