<?php

namespace CudiBundle\Entity\Stock;

use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Article;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Period")
 * @ORM\Table(name="cudi_stock_periods")
 */
class Period
{
    /**
     * @var integer The ID of the period
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Person The person who created the period
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var DateTime The start time of the period
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end time of the period
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param Person $person The person who created the period
     */
    public function __construct(Person $person)
    {
        $this->person = $person;
        $this->startDate = new DateTime();
    }

    /**
     * Get the id of this period
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return boolean
     */
    public function isOpen()
    {
        return $this->endDate == null;
    }

    /**
     * @return self
     */
    public function open()
    {
        $this->endDate = null;

        return $this;
    }

    /**
     * @return self
     */
    public function close()
    {
        $this->endDate = new DateTime();

        return $this;
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
     * @param Article $article
     *
     * @return integer
     */
    public function getNbDelivered(Article $article)
    {
        $value = $this->entityManager
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->getNbDelivered($this, $article);

        return $value < 0 ? 0 : $value;
    }

    /**
     * @param Article $article
     *
     * @return integer
     */
    public function getNbOrdered(Article $article)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->getNbOrdered($this, $article);
    }

    /**
     * @param Article $article
     *
     * @return integer
     */
    public function getNbVirtualOrdered(Article $article)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->getNbVirtualOrdered($this, $article);
    }

    /**
     * @param Article $article
     *
     * @return integer
     */
    public function getNbSold(Article $article)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->getNbSold($this, $article);
    }

    /**
     * @param Article $article
     *
     * @return integer
     */
    public function getNbBooked(Article $article)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->getNbBooked($this, $article);
    }

    /**
     * @param Article $article
     *
     * @return integer
     */
    public function getNbAssigned(Article $article)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->getNbAssigned($this, $article);
    }

    /**
     * @param Article $article
     *
     * @return integer
     */
    public function getNbRetoured(Article $article)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->getNbRetoured($this, $article);
    }

    /**
     * @param Article $article
     *
     * @return integer
     */
    public function getNbQueueOrder(Article $article)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->getNbQueueOrder($article);
    }
}
