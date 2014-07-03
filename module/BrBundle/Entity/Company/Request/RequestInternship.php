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

use BrBundle\Entity\Company\Request,
    BrBundle\Entity\Company\Job,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an event.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company\Request\RequestVacancy")
 * @ORM\Table(name="br.companies_request_internship")
 */
class RequestInternship extends \BrBundle\Entity\Company\Request
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
     * @var string The type of the request
     *
     * @ORM\Column(type="text")
     */
    private $requestType;

    /**
     * @var \BrBundle\Entity\Company\Job
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Company\Job")
     * @ORM\JoinColumn(name="job", referencedColumnName="id")
     */
    private $job;

    /**
     * @var \BrBundle\Entity\Company\Job
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Company\Job")
     * @ORM\JoinColumn(name="edit_job", referencedColumnName="id", nullable=true)
     */
    private $editJob;

    /**
     * @static
     * @var array All the possible requests allowed
     */
    public static $possibleRequests = array(
        'edit' => 'edit',
        'add' => 'add',
        'delete' => 'delete',
    );

    public function __construct(Job $job, $requestType, $contact, Job $editJob = null)
    {
        parent::__construct($contact);
        $this->job = $job;
        $this->_setRequestType($requestType);

        if (null !== $editJob)
            $this->editJob = $editJob;
    }

    private function _setRequestType($type)
    {
        if (!in_array($type, self::$possibleRequests))
            throw new Exception("The requested type does not exist for the vacancy requests");

        $this->requestType = $type;
    }

    public function getJob()
    {
        return $this->job;
    }

    public function getEditJob()
    {
        return $this->editJob;
    }

    public function getRequestType()
    {
        return $this->requestType;
    }

    public function approveRequest()
    {
        switch ($this->requestType) {
            case 'add':
                $this->getJob()->approved();
                break;

            case 'edit':
                $this->getJob()->approve();
                $this->getEditJob()->removed();
                break;

            case 'delete':
                $this->getJob()->removed();
                break;

            default:break;
        }
    }

    public function rejectRequest()
    {
        switch ($this->requestType) {
            case 'add':
                break;

            case 'edit':
                break;

            case 'delete':
                break;

            default:break;
        }
    }
}
