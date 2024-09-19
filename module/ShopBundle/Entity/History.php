<?php

namespace ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ShopBundle\Entity\Session as SalesSession;

/**
 * This entity stores consummations in a sales session.
 *
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\History")
 * @ORM\Table(name="shop_history")
 */
class History
{
    /**
     * @var integer The ID of the history
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     *
     */
    private $id;

    /**
     * @var array An array of all the consummations
     * @ORM\Column(type="array")
     */
    private $reservation;

    /**
     * @var SalesSession The id of the sales session for which this history was made
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Session")
     * @ORM\JoinColumn(name="session", referencedColumnName="id")
     */
    private $salesSession;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param array $reservation
     * @return self
     */
    public function setReservation($reservation)
    {
        $this->reservation = $reservation;

        return $this;
    }

    /**
     * @return array
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * @param  SalesSession $salesSession
     * @return self
     */
    public function setSalesSession(SalesSession $salesSession)
    {
        $this->salesSession = $salesSession;

        return $this;
    }

    /**
     * @return SalesSession
     */
    public function getSalesSession()
    {
        return $this->salesSession;
    }
}
