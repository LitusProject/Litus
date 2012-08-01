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
 
namespace CommonBundle\Entity\Users\Shibboleth;

use Doctrine\ORM\EntityManager;

/**
 * We register the server's hostname as the Shibboleth SP with the KU Leuven.
 * Because of this, however, we need to create an extra step to get the authentication
 * result to Litus.
 * 
 * @Entity(repositoryClass="CommonBundle\Repository\Users\Shibboleth\Code")
 * @Table(name="users.shibboleth_codes")
 */
class Code
{    
    /**
     * @var string The ID of this code
     *
     * @Id
     * @Column(type="string", length=32)
     */
    private $id;

    /**
     * @var \Datetime The time at which this session will end
     *
     * @Column(name="expiration_time", type="datetime")
     */
    private $expirationTime = null;

    /**
     * @var string The authenticated person's university identification
     *
     * @Column(name="university_identification", type="string", length=8)
     */
    private $universityIdentification;
    
    /**
     * @var string The code
     *
     * @Column(type="string", length=32, unique=true)
     */
    private $code;

    /**
     * @param string $universityIdentification The person associated with this session
     * @param string $code The code
     * @param int $expirationTime How long is this code is valid for
     */
    public function __construct($universityIdentification, $code, $expirationTime = null)
    {
        $this->id = md5(uniqid(rand(), true));
        
        $this->expirationTime = new \Datetime(
            'now ' . (($expirationTime < 0) ? '-' : '+') . abs($expirationTime) . ' seconds'
        );
        
        $this->code = $code;
        $this->universityIdentification = $universityIdentification;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Datetime
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    /**
     * @return string
     */
    public function getUniversityIdentification()
    {
        return $this->universityIdentification;
    }
    
    /**
     * Generates a hash from our code.
     *
     * @return string
     */
    public function hash() {
        return sha1($this->code);
    }
    
    /**
     * Checks whether or not this code is valid.
     *
     * Note:
     * We don't delete expired codes here, but wait for the garbage collector to clean up all expired sessions
     * at once.
     *
     * @param string $hash The hash that was received 
     * @return bool
     */
    public function validate($hash)
    {
        $now = new \Datetime();
        if ($this->expirationTime < $now) {
            return false;
        }

        return $hash == sha1($this->code);
    }
}
