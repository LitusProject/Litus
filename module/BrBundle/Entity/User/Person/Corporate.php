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

namespace BrBundle\Entity\User\Person;

use BrBundle\Entity\Company,
    BrBundle\Entity\User\Status\Corporate as CorporateStatus,
    CommonBundle\Component\Util\AcademicYear,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is a person that represents a contact in a company.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\User\Person\Corporate")
 * @ORM\Table(name="users.people_corporate")
 */
class Corporate extends \CommonBundle\Entity\User\Person
{
    /**
     * @var \BrBundle\Entity\Company The user's company
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Company")
     * @ORM\JoinColumn(name="company", referencedColumnName="id")
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity="BrBundle\Entity\User\Status\Corporate", mappedBy="person", cascade={"persist"})
     */
    private $corporateStatuses;

    /**
     * @param \BrBundle\Entity\Company $company     The user's company
     * @param string                   $username    The user's username
     * @param array                    $roles       The user's roles
     * @param string                   $firstName   The user's first name
     * @param string                   $lastName    The user's last name
     * @param string                   $email       The user's e-mail address
     * @param string                   $phoneNumber The user's phone number
     * @param string                   $sex         The users sex
     */
    public function __construct(Company $company, $username, array $roles, $firstName, $lastName, $email, $phoneNumber = null, $sex = null)
    {
        parent::__construct($username, $roles, $firstName, $lastName, $email, $phoneNumber, $sex);

        $this->company = $company;
        $this->corporateStatuses = new ArrayCollection();
    }

    /**
     * @return \BrBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Adds a corporate status to the list, if possible.
     *
     * @param  \BrBundle\Entity\User\Status\Corporate $corporateStatus
     * @return \BrBundle\Entity\User\Person\Corporate
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function addCorporateStatus(CorporateStatus $corporateStatus)
    {
        if (null === $corporateStatus)
            throw new \InvalidArgumentException('Invalid status');

        if (!$this->canHaveCorporateStatus())
            throw \RuntimeException('The corporate status cannot be set');

        $this->corporateStatuses->add($corporateStatus);

        return $this;
    }

    /**
     * If this person already has a corporate status for this academic year, a new
     * one cannot be set.
     *
     * @return bool
     */
    public function canHaveCorporateStatus()
    {
        foreach ($this->corporateStatuses as $corporateStatus) {
            if (AcademicYear::getShortAcademicYear() == $corporateStatus->getYear())
                return false;
        }

        return true;
    }
}
