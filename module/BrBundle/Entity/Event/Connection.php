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

namespace BrBundle\Entity\Event;

use BrBundle\Entity\Event\CompanyMap;
use BrBundle\Entity\Event\Subscription;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Connection
 * Formerly known as Match, but due to php8 this isn't allowed.
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Event\Connection")
 * @ORM\Table(name="br_events_matches", uniqueConstraints={@ORM\UniqueConstraint(name="map_subscription_unique",columns={"companymap", "subscription"})})
 */
class Connection
{
    /**
     * @var integer The ID of the location
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;


    /**
     *@var CompanyMap The companyMap of the matching company at the current event
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Event\CompanyMap")
     * @ORM\JoinColumn(name="companymap", referencedColumnName="id", onDelete="CASCADE")
     */
    private $companyMap;
    

    /**
     *@var Subscription The companyMap of the matching company at the current event
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Event\Subscription")
     * @ORM\JoinColumn(name="subscription", referencedColumnName="id", onDelete="CASCADE")
     */
    private $subscription;


    /**
     * @var DateTime The start date and time of this event.
     *
     * @ORM\Column(name="timestamp", type="datetime")
     */
    private $timestamp;

    /**
     * @var string Notes for the match
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @param CompanyMap   $companyMap
     * @param Subscription $subscription
     */
    public function __construct($companyMap, $subscription)
    {
        $this->companyMap = $companyMap;
        $this->subscription = $subscription;
        $this->timestamp = new DateTime();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return CompanyMap
     */
    public function getCompanyMap()
    {
        return $this->companyMap;
    }

    /**
     * @return Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getStudentCv(EntityManager $em, \CommonBundle\Entity\General\AcademicYear $ay)
    {
        $name = $this->subscription->getFirstName() . ' ' . $this->subscription->getLastName();
        $academic = $em->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findAllByNameQuery($name)
            ->getResult();

        if (!is_null($academic[0])) {
            $entry = $em->getRepository('BrBundle\Entity\Cv\Entry')
                ->findOneByAcademicAndAcademicYear($ay, $academic[0]);

            if (is_null($entry)) {
                return false;
            }
            return $entry;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     * @return self
     */
    public function setNotes(string $notes)
    {
        $this->notes = $notes;

        return $this;
    }
}
