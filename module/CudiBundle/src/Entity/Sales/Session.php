<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Sales;

use CommonBundle\Entity\General\Bank\CashRegister,
    CommonBundle\Entity\Users\Person,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sales\Session")
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
     * @var \DateTime The open date of the sale session
     *
     * @ORM\Column(name="open_date", type="datetime")
     */
    private $openDate;

    /**
     * @var \DateTime The close date of the sale session
     *
     * @ORM\Column(name="close_date", type="datetime", nullable=true)
     */
    private $closeDate;

    /**
     * @var \CommonBundle\Entity\General\Bank\CashRegister The cashregister open status
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Bank\CashRegister")
     * @ORM\JoinColumn(name="open_register", referencedColumnName="id")
     */
    private $openRegister;

    /**
     * @var \CommonBundle\Entity\General\Bank\CashRegister The cashregister close status
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Bank\CashRegister")
     * @ORM\JoinColumn(name="close_register", referencedColumnName="id")
     */
    private $closeRegister;

    /**
     * @var \CommonBundle\Entity\Users\Person The person responsible for the sale session
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
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
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @param \CommonBundle\Entity\General\Bank\CashRegister $openRegister The cash register contents at the start of the session
     * @param \CommonBundle\Entity\Users\Person $manager The manager of the session
     * @param string $comment The comment on this sale session
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
     * @param \DateTime $openDate
     *
     * @return \CudiBundle\Entity\Sales\Session
     */
    public function setOpenDate(DateTime $openDate)
    {
        $this->openDate = $openDate;
        return $this;
    }
    /**
     * @return \DateTime
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
     * @return \CommonBundle\Entity\General\Bank\CashRegister
     */
    public function getOpenRegister()
    {
        return $this->openRegister;
    }

    /**
     * @param \CommonBundle\Entity\General\Bank\CashRegister $closeRegister
     *
     * @return \CudiBundle\Entity\Sales\Session
     */
    public function close(CashRegister $closeRegister)
    {
        $this->closeRegister = $closeRegister;
        $this->closeDate = new DateTime();
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\General\Bank\CashRegister
     */
    public function getCloseRegister()
    {
        return $this->closeRegister;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param string $comment
     *
     * @return \CudiBundle\Entity\Sales\Session
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * @return boolean
     */
    public function isOpen()
    {
        if(null === $this->getCloseDate())
            return true;

        if($this->getCloseDate() >= $this->getOpenDate())
            return false;

        return true;
    }

    /**
     * Calculates the theoretical revenue of a given session --
     * that is, the revenue expected on the basis of sold stock items
     *
     * @return integer
     */
    public function getTheoreticalRevenue()
    {
        return $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\Session')
            ->getTheoreticalRevenue($this);
    }

    /**
     * Calculates the actual revenue of a given session --
     * that is, the register difference between opening and closure of
     * a session
     *
     * @return integer
     */
    public function getActualRevenue()
    {
        if ($this->isOpen())
            return 0;

        return $this->closeRegister->getTotalAmount() - $this->openRegister->getTotalAmount();
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     *
     * @return \CudiBundle\Entity\Sales\Session
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
        return $this;
    }
}
