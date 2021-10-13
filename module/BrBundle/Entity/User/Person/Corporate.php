<?php

namespace BrBundle\Entity\User\Person;

use BrBundle\Entity\Company;
use BrBundle\Entity\User\Status\Corporate as CorporateStatus;
use CommonBundle\Component\Util\AcademicYear;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use RuntimeException;

/**
 * This is a person that represents a contact in a company.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\User\Person\Corporate")
 * @ORM\Table(name="users_people_corporate")
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

    public function __construct()
    {
        parent::__construct();

        $this->corporateStatuses = new ArrayCollection();
    }

    /**
     * @param  Company $company
     * @return self
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Adds a corporate status to the list, if possible.
     *
     * @param  CorporateStatus $corporateStatus
     * @return self
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function addCorporateStatus(CorporateStatus $corporateStatus)
    {
        if ($corporateStatus === null) {
            throw new InvalidArgumentException('Invalid status');
        }

        if (!$this->canHaveCorporateStatus()) {
            throw new RuntimeException('The corporate status cannot be set');
        }

        $this->corporateStatuses->add($corporateStatus);

        return $this;
    }

    /**
     * If this person already has a corporate status for this academic year, a new
     * one cannot be set.
     *
     * @return boolean
     */
    public function canHaveCorporateStatus()
    {
        foreach ($this->corporateStatuses as $corporateStatus) {
            if (AcademicYear::getShortAcademicYear() == $corporateStatus->getYear()) {
                return false;
            }
        }

        return true;
    }
}
