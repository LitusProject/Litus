<?php

namespace ApiBundle\Entity;

use CommonBundle\Component\Acl\RoleAware;
use CommonBundle\Entity\Acl\Role;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores an API key.
 *
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\Key")
 * @ORM\Table(name="api_keys")
 */
class Key implements RoleAware
{
    /**
     * @var integer The ID of this code
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The expire time of this code
     *
     * @ORM\Column(name="expiration_time", type="datetime", nullable=true)
     */
    private $expirationTime;

    /**
     * @var string The host this key's valid for
     *
     * @ORM\Column(type="string")
     */
    private $host;

    /**
     * @var string The code
     *
     * @ORM\Column(type="string", length=32, unique=true)
     */
    private $code;

    /**
     * @var boolean Whether the host should be checked
     *
     * @ORM\Column(name="check_host", type="boolean")
     */
    private $checkHost;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The key's roles
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="api_keys_roles_map",
     *      joinColumns={@ORM\JoinColumn(name="key", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $roles;

    /**
     * @param string  $code
     * @param integer $expirationTime
     */
    public function __construct($code, $expirationTime = /* 5 years */ 157680000)
    {
        $this->expirationTime = new DateTime(
            'now ' . ($expirationTime < 0 ? '-' : '+') . abs($expirationTime) . ' seconds'
        );

        $this->code = $code;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param  string $host
     * @return self
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Revokes the API key.
     *
     * @return void
     */
    public function revoke()
    {
        $this->expirationTime = new DateTime();
    }

    /**
     * @return boolean
     */
    public function getCheckHost()
    {
        return $this->checkHost;
    }

    /**
     * @param  boolean $checkHost
     * @return \ApiBundle\Entity\Key
     */
    public function setCheckHost($checkHost)
    {
        $this->checkHost = $checkHost;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * Add the specified roles to the user.
     *
     * @param  array $roles An array containing the roles that should be added
     * @return \ApiBundle\Entity\Key
     */
    public function setRoles(array $roles)
    {
        $this->roles = new ArrayCollection($roles);

        return $this;
    }

    /**
     * Returns a one-dimensional array containing all roles this user has, without
     * inheritance.
     *
     * @return array
     */
    public function getFlattenedRoles()
    {
        return $this->flattenRolesInheritance(
            $this->getRoles()
        );
    }

    /**
     * Removes the given role.
     *
     * @param  \CommonBundle\Entity\Acl\Role $role The role that should be removed
     * @return \ApiBundle\Entity\Key
     */
    public function removeRole(Role $role)
    {
        $this->roles->removeElement($role);

        return $this;
    }

    /**
     * Checks whether or not this key is valid.
     *
     * @param  string $ip The remote IP
     * @return boolean
     */
    public function validate($ip)
    {
        $now = new DateTime();
        if ($this->expirationTime < $now) {
            return false;
        }

        return !($this->checkHost && gethostbyname($this->host) != $ip);
    }

    /**
     * This method is called recursively to create a one-dimensional role flattening the
     * roles' inheritance structure.
     *
     * @param  array $inheritanceRoles The array with the roles that should be unfolded
     * @param  array $return           The one-dimensional return array
     * @return array
     */
    private function flattenRolesInheritance(array $inheritanceRoles, array $return = array())
    {
        foreach ($inheritanceRoles as $role) {
            if (!in_array($role, $return)) {
                $return[] = $role;
            }
            $return = $this->flattenRolesInheritance($role->getParents(), $return);
        }

        return $return;
    }
}
