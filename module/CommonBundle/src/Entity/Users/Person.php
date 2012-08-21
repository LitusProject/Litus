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

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\Acl\Role,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Code,
    CommonBundle\Entity\Users\Credential,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Zend\Mail\Message,
    Zend\Mail\Transport;

/**
 * This is the entity for a person.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\Users\Person")
 * @Table(
 *      name="users.people",
 *      uniqueConstraints={@UniqueConstraint(name="username_unique", columns={"username"})}
 * )
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="inheritance_type", type="string")
 * @DiscriminatorMap({
 *      "academic"="CommonBundle\Entity\Users\People\Academic",
 *      "corporate"="BrBundle\Entity\Users\People\Corporate",
 *      "supplier"="CudiBundle\Entity\Users\People\Supplier"
 * })
 */
abstract class Person
{
    /**
     * @var string The persons unique identifier
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var string The persons username
     *
     * @Column(type="string", length=50)
     */
    private $username;

    /**
     * @var \CommonBundle\Entity\Users\Credential The person's credential
     *
     * @OneToOne(targetEntity="CommonBundle\Entity\Users\Credential", cascade={"all"}, fetch="EAGER")
     * @JoinColumn(name="credential", referencedColumnName="id")
     */
    private $credential;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection;
     *
     * @ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @JoinTable(
     *      name="users.people_roles_map",
     *      joinColumns={@JoinColumn(name="person", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $roles;

    /**
     * @var string The persons first name
     *
     * @Column(name="first_name", type="string", length=30)
     */
    private $firstName;

    /**
     * @var string The persons last name
     *
     * @Column(name="last_name", type="string", length=30)
     */
    private $lastName;

    /**
     * @var string The users email address.
     *
     * @Column(type="string", nullable=true, length=100)
     */
    private $email;

    /**
     * @var \CommonBundle\Entity\General\Address The address of the supplier
     *
     * @OneToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist"})
     * @JoinColumn(name="address", referencedColumnName="id")
     */
    private $address;

    /**
     * @var string The persons telephone number
     *
     * @Column(type="string", length=15, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string The persons sex ('m' or 'f')
     *
     * @Column(type="string", length=1, nullable=true)
     */
    private $sex;

    /**
     * @var bool Whether or not this can login
     *
     * @Column(name="can_login", type="boolean")
     */
    private $canLogin;

    /**
     * @OneToMany(targetEntity="CommonBundle\Entity\Users\Statuses\Organization", mappedBy="person")
     */
    private $organisationStatuses;

    /**
     * @OneToMany(targetEntity="CommonBundle\Entity\Users\Barcode", mappedBy="person")
     * @OrderBy({"time" = "ASC"})
     */
    private $barcodes;

    /**
     * @var \CommonBundle\Entity\Users\Code A unique code to activate this account
     *
     * @OneToOne(targetEntity="CommonBundle\Entity\Users\Code")
     * @JoinColumn(name="code", referencedColumnName="id")
     */
    private $code;

    /**
     * @var integer The number of failed logins.
     *
     * @Column(name="failed_logins", type="smallint")
     */
    private $failedLogins = 0;

    /**
     * @var \CommonBundle\Entity\General\Language The last used language of this person
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @param string $username The user's username
     * @param array $roles The user's roles
     * @param string $firstName The user's first name
     * @param string $lastName The user's last name
     * @param string $email The user's e-mail address
     * @param string $phoneNumber The user's phone number
     * @param $sex string The users sex ('m' or 'f')
     */
    public function __construct($username, array $roles, $firstName, $lastName, $email = null, $phoneNumber = null, $sex = null)
    {
        $this->setUsername($username);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);
        $this->setPhoneNumber($phoneNumber);
        $this->setSex($sex);

        $this->canLogin = true;

        $this->roles = new ArrayCollection($roles);
        $this->organisationStatuses = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $username
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setUsername($username)
    {
        if (($username === null) || !is_string($username))
            throw new \InvalidArgumentException('Invalid username');

        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param \CommonBundle\Entity\Users\Credential $credential
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setCredential(Credential $credential)
    {
        $this->credential = $credential;
        return $this;
    }

    /**
     * @return string
     */
    public function getCredential()
    {
        return $this->credential->getCredential();
    }

    /**
     * Checks whether or not the given credential is valid.
     *
     * @param string $credential The credential that should be checked
     * @return bool
     */
    public function validateCredential($credential)
    {
        if (null == $this->credential)
            return false;

        return $this->credential->validateCredential($credential);
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
     * @param array $roles An array containing the roles that should be added
     * @return \CommonBundle\Entity\Users\Person
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
        return $this->_flattenRolesInheritance(
            $this->roles->toArray()
        );
    }

    /**
     * Removes the given role.
     *
     * @param \CommonBundle\Entity\Acl\Role $role The role that should be removed
     * @return \CommonBundle\Entity\Users\Person
     */
    public function removeRole(Role $role)
    {
        $this->roles->removeElement($role);
        return $this;
    }

    /**
     * @param string $firstName
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $lastName
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @param string $email
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setEmail($email = null)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param \CommonBundle\Entity\General\Address $address
     * @return \CommonBundle\Entity\Users\Person
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\General\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param null|string $phoneNumber
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setPhoneNumber($phoneNumber = null)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param null|string $sex The person's sex
     * @return \CommonBundle\Entity\Users\Person
     * @throws \InvalidArgumentException
     */
    public function setSex($sex)
    {
        if(($sex !== 'm') && ($sex !== 'f') && ($sex !== null))
            throw new \InvalidArgumentException('Invalid sex');

        $this->sex = $sex;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @return bool
     */
    public function canLogin()
    {
        return $this->canLogin;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function disableLogin()
    {
        $this->canLogin = false;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\Users\Barcode
     */
    public function getBarcode()
    {
        return isset($this->barcodes[0]) ? $this->barcodes[0] : null;
    }

    /**
     * @return \CommonBundle\Entity\Users\Code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param \CommonBundle\Entity\Users\Code|null $code
     *
     * @return \CommonBundle\Entity\Users\Person
     */
    public function setCode(Code $code = null)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return integer
     */
    public function getFailedLogins()
    {
        return $this->failedLogins;
    }

    /**
     * @param integer $failedLogins
     *
     * @return \CommonBundle\Entity\Users\Person
     */
    public function setFailedLogins($failedLogins)
    {
        $this->failedLogins = $failedLogins;
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return \CommonBundle\Entity\Users\Person
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\General\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \Zend\Mail\Transport $mailTransport
     * @param boolean $onlyShibboleth Activate only login by Shibboleth
     *
     * @return \CommonBundle\Entity\Users\Person
     */
    public function activate(EntityManager $entityManager, Transport $mailTransport, $onlyShibboleth = true)
    {
        if ($onlyShibboleth) {
            $this->canlogin = true;
        } else {
            do {
                $code = md5(uniqid(rand(), true));
                $found = $entityManager
                    ->getRepository('CommonBundle\Entity\Users\Code')
                    ->findOneByCode($code);
            } while(isset($found));

            $code = new Code($code);
            $entityManager->persist($code);
            $this->setCode($code);

            $message = $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('account_activated_mail');

            $subject = $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('account_activated_subject');

            $mailAddress = $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('system_mail_address');

            $mailName = $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('system_mail_name');

            $mail = new Message();
            $mail->setBody(str_replace(array('{{ username }}', '{{ name }}', '{{ code }}'), array($this->getUserName(), $this->getFullName(), $code->getCode()), $message))
                ->setFrom($mailAddress, $mailName)
                ->addTo($this->getEmail(), $this->getFullName())
                ->setSubject($subject);

            if ('production' == getenv('APPLICATION_ENV'))
                $mailTransport->send($mail);
        }

        return $this;
    }

    /**
     * This method is called recursively to create a one-dimensional role flattening the
     * roles' inheritance structure.
     *
     * @param array $inheritanceRoles The array with the roles that should be unfolded
     * @param array $return The one-dimensional return array
     * @return array
     */
    private function _flattenRolesInheritance(array $inheritanceRoles, array $return = array())
    {
        foreach ($inheritanceRoles as $role) {
            $return[] = $role;
            $return = $this->_flattenRolesInheritance($role->getParents(), $return);
        }

        return $return;
    }

    /**
     * Checks whether or not this person is a member.
     *
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @return boolean
     */
    public function isMember(AcademicYear $academicYear)
    {
        foreach ($this->organisationStatuses as $status) {
            if ($academicYear == $status->getAcademicYear() && 'non_member' != $status->getStatus())
                return true;
        }

        return false;
    }
}
