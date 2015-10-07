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

namespace BrBundle\Entity\Company;

use BrBundle\Entity\User\Person\Corporate,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company\Request")
 * @ORM\Table(name="br.companies_request")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "vacanyRequest"="BrBundle\Entity\Company\Request\RequestVacancy",
 *      "internshipRequest"="BrBundle\Entity\Company\Request\RequestInternship"
 * })
 */
abstract class Request
{
    /**
     * @var request's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Corporate The contact used in this order
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\User\Person\Corporate")
     * @ORM\JoinColumn(name="contact", referencedColumnName="id")
     */
    private $contact;

    /**
     * @var DateTime The time of creation of this node
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var boolean True if the request has been handled, false if not.
     *
     * @ORM\Column(type="boolean")
     */
    private $handled;

    /**
     * @param Corporate $contact
     */
    public function __construct(Corporate $contact)
    {
        $this->creationTime = new DateTime();
        $this->contact = $contact;
        $this->handled = false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $result = 'pending';

        if ($this->handled) {
            $result = 'rejected';

            if ($this->getJob()->isApproved()) {
                $result = 'approved';
            }
        }

        return $result;
    }

    /**
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return null
     */
    public function handled()
    {
        $this->handled = true;
    }

    /**
     * @return null
     */
    abstract public function approveRequest();

    /**
     * @return null
     */
    abstract public function rejectRequest($message);

    /**
     * @return \BrBundle\Entity\Company\Job
     */
    abstract public function getJob();

    /**
     * @return String
     */
    abstract public function getRejectMessage();
}
