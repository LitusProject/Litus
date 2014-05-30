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

namespace BrBundle\Entity\Company\Request;

use BrBundle\Entity\Company\Job,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an event.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company\Request\VacancyRequest")
 * @ORM\Table(name="br.companies_vacancy_request")
 */
class VacancyRequest
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
     * @var \BrBundle\Entity\User\Person\Corporate The contact used in this order
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\User\Person\Corporate")
     * @ORM\JoinColumn(name="contact", referencedColumnName="id")
     */
    private $contact;

    /**
     * @static
     * @var array All the possible requests allowed
     */
    public static $possibleRequests = array(
        'edit' => 'edit',
        'add' => 'add',
        'delete' => 'delete',
    );

    /**
     * @var string The type of the request
     *
     * @ORM\Column(type="text")
     */
    private $requestType;

	/**
     * @var BrBundle\Entity\Company\Job
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Company\Job")
     * @ORM\JoinColumn(name="job", referencedColumnName="id")
     */
    private $job;

    /**
     * @var \DateTime The time of creation of this node
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var string The type of the request
     *
     * @ORM\Column(type="boolean")
     */
    private $handled;

	public function __construct(Job $job, $requestType, $contact)
    {
        $this->job = $job;
        $this->creationTime = new DateTime();
        $this->requestType = $requestType;
        $this->contact = $contact;
        $this->handled = false;
    }

    /**
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    public function getJob()
    {
        return $this->job;
    }

    public function getRequestType()
    {
        return $this->requestType;
    }

    public function approveRequest()
    {

    }

    public function rejectRequest()
    {

    }
}
