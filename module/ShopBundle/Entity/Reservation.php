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

namespace ShopBundle\Entity;

use CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a reservation.
 *
 * @author Floris Kint <floris.kint@litus.cc>
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\Reservation")
 * @ORM\Table(name="shop.reservations")
 */
class Reservation
{
    /**
     * @var integer The ID of this reservation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Product The product of this reservation
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Product")
     * @ORM\JoinColumn(name="product", referencedColumnName="id")
     */
    private $product;

    /**
     * @var integer The amount of products reserved
     *
     * @ORM\Column(type="bigint")
     */
    private $amount;

    /**
     * @var SalesSession The id of the sales session for which this reservation was made
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\SalesSession")
     * @ORM\JoinColumn(name="session", referencedColumnName="id")
     */
    private $salesSession;

    /**
     * @var boolean Whether the person reserving has not come to get his reservation
     *
     * @ORM\Column(type="boolean")
     */
    private $noShow;

    /**
     * @var Person The person who made the reservation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var DateTime The date this reservation was made on
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  Product $product
     * @return self
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
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

    /**
     * @param  integer $amount
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param  Person $person
     * @return self
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param  boolean $noShow
     * @return self
     */
    public function setNoShow($noShow)
    {
        $this->noShow = $noShow;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getNoShow()
    {
        return $this->noShow;
    }

    /**
     * @param  DateTime $timestamp
     * @return self
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return boolean
     */
    public function canCancel()
    {
        $timestamp = new DateTime();

        return $timestamp < $this->getSalesSession()->getStartDate();
    }
}
