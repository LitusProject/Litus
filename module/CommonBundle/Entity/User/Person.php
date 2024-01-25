<?php

namespace CommonBundle\Entity\User;

use CommonBundle\Component\Acl\RoleAware;
use CommonBundle\Entity\Acl\Role;
use CommonBundle\Entity\General\AcademicYear as AcademicYearEntity;
use CommonBundle\Entity\General\Address;
use CommonBundle\Entity\General\Language;
use CommonBundle\Entity\User\Status\Organization as OrganizationStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\TransportInterface;

/**
 * This is the entity for a person.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Person")
 * @ORM\Table(
 *      name="users_people",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="users_people_username", columns={"username"})}
 * )
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "academic"="CommonBundle\Entity\User\Person\Academic",
 *      "corporate"="BrBundle\Entity\User\Person\Corporate",
 *      "supplier"="CudiBundle\Entity\User\Person\Supplier"
 * })
 */
abstract class Person implements RoleAware
{
    /**
     * @var integer The persons unique identifier
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
     * @var Credential The person's credential
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\User\Credential", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="credential", referencedColumnName="id")
     */
    private $credential;

    /**
     * @var ArrayCollection The person's roles
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="users_people_roles_map",
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
     * @var Address The address of the user
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
     * @var string|null The persons sex ('m' or 'f')
     *
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $sex;

    /**
     * @var boolean Whether or not this user can login
     *
     * @ORM\Column(name="can_login", type="boolean")
     */
    private $canLogin;

    /**
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\User\Status\Organization", mappedBy="person", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $organizationStatuses;

    /**
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\User\Barcode", mappedBy="person", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\OrderBy({"creationTime" = "ASC"})
     */
    private $barcodes;

    /**
     * @var Code A unique code to activate this account
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
     * @var Language The last used language of this person
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    public function __construct()
    {
        $this->canLogin = true;

        $this->roles = new ArrayCollection();
        $this->organizationStatuses = new ArrayCollection();
        $this->barcodes = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string $username
     * @return self
     * @throws InvalidArgumentException
     */
    public function setUsername($username)
    {
        if (($username === null) || !is_string($username)) {
            throw new InvalidArgumentException('Invalid username');
        }

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
     * @param  Credential $credential
     * @return self
     * @throws InvalidArgumentException
     */
    public function setCredential(Credential $credential)
    {
        $this->credential = $credential;

        return $this;
    }

    /**
     * @return boolean
     */
    public function hasCredential()
    {
        return $this->credential !== null;
    }

    /**
     * @return Credential
     */
    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * Checks whether or not the given credential is valid.
     *
     * @param  string $credential The credential that should be checked
     * @return boolean
     */
    public function validateCredential($credential)
    {
        if ($this->credential == null) {
            return false;
        }

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
     * @param  array $roles An array containing the roles that should be added
     * @return self
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

    public function getSystemRoles()
    {
        return array_filter(
            $this->getFlattenedRoles(),
            function (Role $role) {
                return $role->getSystem();
            }
        );
    }

    /**
     * Removes the given role.
     *
     * @param  Role $role The role that should be removed
     * @return self
     */
    public function removeRole(Role $role)
    {
        $this->roles->removeElement($role);

        return $this;
    }

    /**
     * @param  string $firstName
     * @return self
     * @throws InvalidArgumentException
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
     * @param  string $lastName
     * @return self
     * @throws InvalidArgumentException
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
     * @param  string|null $email
     * @return self
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
     * @param  Address $address
     * @return self
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param  string|null $phoneNumber
     * @return self
     * @throws InvalidArgumentException
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
     * @param  string|null $sex The person's sex
     * @return self
     * @throws InvalidArgumentException
     */
    public function setSex($sex)
    {
        if (($sex !== 'm') && ($sex !== 'f') && ($sex !== null)) {
            throw new InvalidArgumentException('Invalid sex');
        }

        $this->sex = $sex;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @return boolean
     */
    public function canLogin()
    {
        return $this->canLogin;
    }

    /**
     * @return self
     */
    public function disableLogin()
    {
        $this->canLogin = false;

        return $this;
    }

    /**
     * @return Barcode
     */
    public function getBarcode()
    {
        return $this->barcodes[0] ?? null;
    }

    /**
     * @param  Barcode $code
     * @return self
     */
    public function addBarcode(Barcode $code)
    {
        foreach ($this->barcodes as $barcode) {
            if ($code->getBarcode() === $barcode->getBarcode()) {
                return $this;
            }
        }

        $this->barcodes->add($code);

        return $this;
    }

    /**
     * @return Code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param Code|null $code
     *
     * @return self
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
     * @return self
     */
    public function setFailedLogins($failedLogins)
    {
        // Limit of Postgres smallint datatype
        if ($failedLogins > 32767) {
            $failedLogins = 32767;
        }

        $this->failedLogins = $failedLogins;

        return $this;
    }

    /**
     * @param Language $language
     *
     * @return self
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param  OrganizationStatus $organizationStatus
     * @return self
     */
    public function addOrganizationStatus(OrganizationStatus $organizationStatus)
    {
        $this->organizationStatuses->add($organizationStatus);

        return $this;
    }

    /**
     * @param  OrganizationStatus $organizationStatus
     * @return self
     */
    public function removeOrganizationStatus(OrganizationStatus $organizationStatus)
    {
        $this->organizationStatuses->removeElement($organizationStatus);

        return $this;
    }

    /**
     * @param  AcademicYearEntity $academicYear
     * @return OrganizationStatus
     */
    public function getOrganizationStatus(AcademicYearEntity $academicYear)
    {
        foreach ($this->organizationStatuses as $status) {
            if ($status->getAcademicYear() == $academicYear) {
                return $status;
            }
        }

        return null;
    }

    /**
     * @param  AcademicYearEntity $academicYear
     * @return boolean
     */
    public function canHaveOrganizationStatus(AcademicYearEntity $academicYear)
    {
        if ($this->organizationStatuses->count() >= 1) {
            if ($this->organizationStatuses->exists(
                function ($key, $value) use ($academicYear) {
                    if ($value->getAcademicYear() == $academicYear) {
                        return true;
                    }
                }
            )
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks whether or not this person is a member.
     *
     * @param  AcademicYearEntity $academicYear
     * @return boolean
     */
    public function isMember(AcademicYearEntity $academicYear)
    {
        if ($this->getOrganizationStatus($academicYear) !== null) {
            return !($this->getOrganizationStatus($academicYear)->getStatus() == 'non_member');
        }

        return false;
    }

    /**
     * Checks whether or not this person is a praesidium member.
     *
     * @param  AcademicYearEntity $academicYear
     * @return boolean
     */
    public function isPraesidium(AcademicYearEntity $academicYear)
    {
        if ($this->getOrganizationStatus($academicYear) !== null) {
            if ($this->getOrganizationStatus($academicYear)->getStatus() == 'praesidium') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param EntityManager      $entityManager
     * @param TransportInterface $mailTransport
     * @param boolean            $onlyShibboleth Activate only login by Shibboleth
     * @param string             $messageConfig  The config key for the mail
     * @param integer            $time           The expiration time of the activation code
     *
     * @return self
     */
    public function activate(EntityManager $entityManager, TransportInterface $mailTransport, $onlyShibboleth = true, $messageConfig = 'common.account_activated_mail', $time = 604800)
    {
        if ($onlyShibboleth) {
            $this->canLogin = true;
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
            $entityManager->flush();

            $language = $this->getLanguage();
            if ($language === null) {
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
            $mail->setEncoding('UTF-8')
                ->setBody(str_replace(array('{{ username }}', '{{ name }}', '{{ code }}'), array($this->getUserName(), $this->getFullName(), $code->getCode()), $message))
                ->setFrom($mailAddress, $mailName)
                ->addTo($this->getEmail(), $this->getFullName())
                ->setSubject($subject);

            if (getenv('APPLICATION_ENV') != 'development') {
                $mailTransport->send($mail);
            }
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
