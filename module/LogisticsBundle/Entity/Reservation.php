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

namespace LogisticsBundle\Entity;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use LogisticsBundle\Entity\Reservation\Resource;

/**
 * This is the entity for a reservation.
 *
 * A reservation is associated with a certain resource and locks it from a given start date to a given end date.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Reservation")
 * @ORM\Table(name="logistics_reservations")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *     "piano"="LogisticsBundle\Entity\Reservation\Piano",
 *     "van"="LogisticsBundle\Entity\Reservation\Van"
 * })
 */
abstract class Reservation
{
    /**
     * @var integer The reservation's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Person The creator of this reservation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="creator", referencedColumnName="id")
     */
    private $creator;

    // phpcs:disable Squiz.Commenting.FunctionComment.IncorrectParamVarName
    /**
     * @var Resource The resource associated with this reservation.
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Reservation\Resource", inversedBy="reservations")
     * @ORM\JoinColumn(name="resource_name", referencedColumnName="name")
     */
    // phpcs:enable
    private $resource;

    /**
     * @var string The reason for this reservation
     *
     * @ORM\Column(type="text")
     */
    private $reason;

    /**
     * @var string Additional information for this reservation
     *
     * @ORM\Column(name="additional_info", type="text")
     */
    private $additionalInfo;

    /**
     * @var DateTime The start date and time of this reservation.
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date and time of this reservation.
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    // phpcs:disable Squiz.Commenting.FunctionComment.IncorrectParamVarName
    /**
     * @param Resource $resource
     * @param Person   $creator
     */
    // phpcs:enable
    public function __construct(Resource $resource, Person $creator)
    {
        $this->creator = $creator;
        $this->resource = $resource;
        $this->additionalInfo = '';
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    // phpcs:disable Squiz.Commenting.FunctionComment.IncorrectParamVarName
    /**
     * @return Resource
     */
    // phpcs:enable
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return Person
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param  string $reason
     * @return self
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param  DateTime $startDate
     * @return self
     */
    public function setStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param  DateTime $endDate
     * @return self
     */
    public function setEndDate(DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param  string $additionalInfo
     * @return self
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalInfo()
    {
        return $this->additionalInfo;
    }
}
