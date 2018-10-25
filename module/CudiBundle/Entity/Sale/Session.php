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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Sale;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\General\Bank\CashRegister;
use CommonBundle\Entity\General\Organization;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Session")
 * @ORM\Table(name="cudi.sales_sessions")
 */
class Session
{
    /**
     * @var integer The ID of the sale session
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The open date of the sale session
     *
     * @ORM\Column(name="open_date", type="datetime")
     */
    private $openDate;

    /**
     * @var DateTime The close date of the sale session
     *
     * @ORM\Column(name="close_date", type="datetime", nullable=true)
     */
    private $closeDate;

    /**
     * @var CashRegister The cashregister open status
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Bank\CashRegister")
     * @ORM\JoinColumn(name="open_register", referencedColumnName="id")
     */
    private $openRegister;

    /**
     * @var CashRegister The cashregister close status
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Bank\CashRegister")
     * @ORM\JoinColumn(name="close_register", referencedColumnName="id")
     */
    private $closeRegister;

    /**
     * @var Person The person responsible for the sale session
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="manager", referencedColumnName="id")
     */
    private $manager;

    /**
     * @var string The comment on this sale session
     *
     * @ORM\Column(type="string")
     */
    private $comment;

    /**
     * @var ArrayCollection The restrictions of this sale session
     *
     * @ORM\OneToMany(targetEntity="CudiBundle\Entity\Sale\Session\Restriction", mappedBy="session")
     */
    private $restrictions;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param CashRegister $openRegister The cash register contents at the start of the session
     * @param Person       $manager      The manager of the session
     * @param string       $comment      The comment on this sale session
     */
    public function __construct(CashRegister $openRegister, Person $manager, $comment = '')
    {
        $this->openDate = new DateTime();
        $this->openRegister = $openRegister;
        $this->comment = $comment;
        $this->manager = $manager;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param DateTime $openDate
     *
     * @return self
     */
    public function setOpenDate(DateTime $openDate)
    {
        $this->openDate = $openDate;

        return $this;
    }
    /**
     * @return DateTime
     */
    public function getOpenDate()
    {
        return $this->openDate;
    }

    /**
     * @return DateTime
     */
    public function getCloseDate()
    {
        return $this->closeDate;
    }

    /**
     * @return CashRegister
     */
    public function getOpenRegister()
    {
        return $this->openRegister;
    }

    /**
     * @param CashRegister $closeRegister
     *
     * @return self
     */
    public function close(CashRegister $closeRegister)
    {
        $this->closeRegister = $closeRegister;
        $this->closeDate = new DateTime();

        return $this;
    }

    /**
     * @return CashRegister
     */
    public function getCloseRegister()
    {
        return $this->closeRegister;
    }

    /**
     * @return Person
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param string $comment
     *
     * @return self
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return boolean
     */
    public function isOpen()
    {
        if (null === $this->getCloseDate()) {
            return true;
        }

        return ($this->getCloseDate() < $this->getOpenDate());
    }

    /**
     * @param  Organization|null $organization
     * @return integer
     */
    public function getTheoreticalRevenue(Organization $organization = null)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->getTheoreticalRevenue($this, $organization);
    }

    /**
     * @return integer
     */
    public function getActualRevenue()
    {
        if ($this->isOpen()) {
            return 0;
        }

        return $this->closeRegister->getTotalAmount() - $this->openRegister->getTotalAmount();
    }

    /**
     * @param  Organization|null $organization
     * @return integer
     */
    public function getPurchasedAmount(Organization $organization = null)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->getPurchasedAmountBySession($this, $organization);
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return self
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * @return integer
     */
    public function getNumberSaleItems()
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\SaleItem')
            ->findNumberBySession($this);
    }

    /**
     * @return integer
     */
    public function getNumberReturnItems()
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\ReturnItem')
            ->findNumberBySession($this);
    }

    /**
     * @param EntityManager $entityManager
     * @param Person        $person
     *
     * @return boolean
     */
    public function canSignIn(EntityManager $entityManager, Person $person)
    {
        foreach ($this->restrictions as $restriction) {
            if (!$restriction->canSignIn($entityManager, $person)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return AcademicYear::getUniversityYear($this->entityManager, $this->getOpenDate());
    }
}
