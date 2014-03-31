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

namespace CommonBundle\Entity\User;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\Acl\Role,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Code,
    CommonBundle\Entity\User\Credential,
    CommonBundle\Entity\User\Status\Organization as OrganizationStatus,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM,
    Zend\Mail\Message,
    Zend\Mail\Transport\TransportInterface;

/**
 * This is the entity for a person.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Person")
 * @ORM\Table(
 *      name="users.people",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="username_unique", columns={"username"})}
 * )
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "academic"="CommonBundle\Entity\User\Person\Academic",
 *      "corporate"="BrBundle\Entity\User\Person\Corporate",
 *      "supplier"="CudiBundle\Entity\User\Person\Supplier"
 * })
 */
abstract class Person
{
    /**
     * @var string The persons unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The persons username
     *
     * @ORM\Column(type="string", length=50)
     */
    private $username;

    /**
     * @var \CommonBundle\Entity\User\Credential The person's credential
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\User\Credential", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="credential", referencedColumnName="id")
     */
    private $credential;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection;
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="users.people_roles_map",
     *      joinColumns={@ORM\JoinColumn(name="person", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $roles;

    /**
     * @var string The persons first name
     *
     * @ORM\Column(name="first_name", type="string", length=50)
     */
    private $firstName;

    /**
     * @var string The persons last name
     *
     * @ORM\Column(name="last_name", type="string", length=50)
     */
    private $lastName;

    /**
     * @var string The users email address.
     *
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    private $email;

    /**
     * @var \CommonBundle\Entity\General\Address The address of the user
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist"})
     * @ORM\JoinColumn(name="address", referencedColumnName="id")
     */
    private $address;

    /**
     * @var string The persons telephone number
     *
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string The persons sex ('m' or 'f')
     *
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $sex;

    /**
     * @var bool Whether or not this user can login
     *
     * @ORM\Column(name="can_login", type="boolean")
     */
    private $canLogin;

    /**
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\User\Status\Organization", mappedBy="person", cascade={"persist", "remove"})
     */
    private $organizationStatuses;

    /**
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\User\Barcode", mappedBy="person")
     * @ORM\OrderBy({"time" = "ASC"})
     */
    private $barcodes;

    /**
     * @var \CommonBundle\Entity\User\Code A unique code to activate this account
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\User\Code")
     * @ORM\JoinColumn(name="code", referencedColumnName="id")
     */
    private $code;

    /**
     * @var integer The number of failed logins.
     *
     * @ORM\Column(name="failed_logins", type="smallint")
     */
    private $failedLogins = 0;

    /**
     * @var \CommonBundle\Entity\General\Language The last used language of this person
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @param string $username    The user's username
     * @param array  $roles       The user's roles
     * @param string $firstName   The user's first name
     * @param string $lastName    The user's last name
     * @param string $email       The user's e-mail address
     * @param string $phoneNumber The user's phone number
     * @param string $sex         string The users sex ('m' or 'f')
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
        $this->organizationStatuses = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string                           $username
     * @return \CommonBundle\Entity\User\Person
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
     * @param  \CommonBundle\Entity\User\Credential $credential
     * @return \CommonBundle\Entity\User\Person
     * @throws \InvalidArgumentException
     */
    public function setCredential(Credential $credential)
    {
        $this->credential = $credential;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasCredential()
    {
        return null !== $this->credential;
    }

    /**
     * @return \CommonBundle\Entity\User\Credential
     */
    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * Checks whether or not the given credential is valid.
     *
     * @param  string $credential The credential that should be checked
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
     * @param  array                            $roles An array containing the roles that should be added
     * @return \CommonBundle\Entity\User\Person
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
            $this->getRoles()
        );
    }

    /**
     * Removes the given role.
     *
     * @param  \CommonBundle\Entity\Acl\Role    $role The role that should be removed
     * @return \CommonBundle\Entity\User\Person
     */
    public function removeRole(Role $role)
    {
        $this->roles->removeElement($role);

        return $this;
    }

    /**
     * @param  string                           $firstName
     * @return \CommonBundle\Entity\User\Person
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
     * @param  string                           $lastName
     * @return \CommonBundle\Entity\User\Person
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
     * @param  string                           $email
     * @return \CommonBundle\Entity\User\Person
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
     * @param  \CommonBundle\Entity\General\Address $address
     * @return \CommonBundle\Entity\User\Person
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
     * @param  null|string                      $phoneNumber
     * @return \CommonBundle\Entity\User\Person
     * @throws \InvalidArgumentException
     */
    public function setPhoneNumber($phoneNumber = null)
    {
        $phoneNumber = str_replace(' ', '', $phoneNumber);
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param  null|string                      $sex The person's sex
     * @return \CommonBundle\Entity\User\Person
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
     * @return \CommonBundle\Entity\User\Person
     */
    public function disableLogin()
    {
        $this->canLogin = false;

        return $this;
    }

    /**
     * @return \CommonBundle\Entity\User\Barcode
     */
    public function getBarcode()
    {
        return isset($this->barcodes[0]) ? $this->barcodes[0] : null;
    }

    /**
     * @return \CommonBundle\Entity\User\Code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param \CommonBundle\Entity\User\Code|null $code
     *
     * @return \CommonBundle\Entity\User\Person
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
     * @return \CommonBundle\Entity\User\Person
     */
    public function setFailedLogins($failedLogins)
    {
        if($failedLogins > 32767) // Limit of Postgres smallint datatype
            $failedLogins = 32767;
        $this->failedLogins = $failedLogins;

        return $this;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return \CommonBundle\Entity\User\Person
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
     * @param  \CommonBundle\Entity\User\Status\Organization $organizationStatus
     * @return \CommonBundle\Entity\User\Person
     */
    public function addOrganizationStatus(OrganizationStatus $organizationStatus)
    {
        $this->organizationStatuses->add($organizationStatus);

        return $this;
    }

    /**
     * @param  \CommonBundle\Entity\User\Status\Organization $organizationStatus
     * @return \CommonBundle\Entity\User\Person
     */
    public function removeOrganizationStatus(OrganizationStatus $organizationStatus)
    {
        $this->organizationStatuses->removeElement($organizationStatus);

        return $this;
    }

    /**
     * @param  \CommonBundle\Entity\General\AcademicYear     $academicYear
     * @return \CommonBundle\Entity\User\Status\Organization
     */
    public function getOrganizationStatus(AcademicYearEntity $academicYear)
    {
        foreach ($this->organizationStatuses as $status) {
            if ($status->getAcademicYear() == $academicYear)
                return $status;
        }

        return null;
    }

    /**
     * @param  \CommonBundle\Entity\General\AcademicYear $academicYear
     * @return boolean
     */
    public function canHaveOrganizationStatus(AcademicYearEntity $academicYear)
    {
        if ($this->organizationStatuses->count() >= 1) {
            if ($this->organizationStatuses->exists(
                function ($key, $value) use ($academicYear) {
                    if ($value->getAcademicYear() == $academicYear)
                        return true;
                }
            )) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks whether or not this person is a member.
     *
     * @param  \CommonBundle\Entity\General\AcademicYear $academicYear
     * @return boolean
     */
    public function isMember(AcademicYearEntity $academicYear)
    {
        if (null !== $this->getOrganizationStatus($academicYear)) {
            if ($this->getOrganizationStatus($academicYear) == 'non_member')
                return false;
            return true;
        }

        return false;
    }

    /**
     * Checks whether or not this person is a praesidium member.
     *
     * @param  \CommonBundle\Entity\General\AcademicYear $academicYear
     * @return boolean
     */
    public function isPraesidium(AcademicYearEntity $academicYear)
    {
        if (null !== $this->getOrganizationStatus($academicYear)) {
            if ($this->getOrganizationStatus($academicYear)->getStatus() == 'praesidium')
                return true;
        }

        return false;
    }

    /**
     * @param \Doctrine\ORM\EntityManager             $entityManager
     * @param \Zend\Mail\Transport\TransportInterface $mailTransport
     * @param boolean                                 $onlyShibboleth Activate only login by Shibboleth
     *
     * @return \CommonBundle\Entity\User\Person
     */
    public function activate(EntityManager $entityManager, TransportInterface $mailTransport, $onlyShibboleth = true, $messageConfig = 'common.account_activated_mail', $time = 604800)
    {
        if ($onlyShibboleth) {
            $this->canlogin = true;
        } else {
            do {
                $code = md5(uniqid(rand(), true));
                $found = $entityManager
                    ->getRepository('CommonBundle\Entity\User\Code')
                    ->findOneByCode($code);
            } while (isset($found));

            $code = new Code($code, $time);
            $entityManager->persist($code);
            $this->setCode($code);

            if (!($language = $this->getLanguage())) {
                $language = $entityManager->getRepository('CommonBundle\Entity\General\Language')
                    ->findOneByAbbrev('en');
            }

            $mailData = unserialize(
                $entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue($messageConfig)
            );

            $message = $mailData[$language->getAbbrev()]['content'];
            $subject = $mailData[$language->getAbbrev()]['subject'];

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

            if ('development' != getenv('APPLICATION_ENV'))
                $mailTransport->send($mail);
        }

        return $this;
    }

    /**
     * This method is called recursively to create a one-dimensional role flattening the
     * roles' inheritance structure.
     *
     * @param  array $inheritanceRoles The array with the roles that should be unfolded
     * @param  array $return           The one-dimensional return array
     * @return array
     */
    private function _flattenRolesInheritance(array $inheritanceRoles, array $return = array())
    {
        foreach ($inheritanceRoles as $role) {
            if (!in_array($role, $return))
                $return[] = $role;
            $return = $this->_flattenRolesInheritance($role->getParents(), $return);
        }

        return $return;
    }
}
