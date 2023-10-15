<?php

namespace BrBundle\Entity\Company;

use BrBundle\Entity\User\Person\Corporate;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company\Request")
 * @ORM\Table(name="br_companies_requests")
 */
class Request
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
     * @var string The type of the request
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $requestType;

    /**
     * @var Job
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Company\Job")
     * @ORM\JoinColumn(name="job", referencedColumnName="id")
     */
    private $job;

    /**
     * @var Job
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Company\Job")
     * @ORM\JoinColumn(name="edit_job", referencedColumnName="id", nullable=true)
     */
    private $editJob;

    /**
     * @var string The type of the request
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $rejectMessage;

    /**
     * @static
     * @var array All the possible requests allowed
     */

    public static $possibleRequests = array(
        'edit'        => 'edit',
        'edit reject' => 'edit reject',
        'add'         => 'add',
    );

    /**
     * @param Job       $job
     * @param string    $requestType
     * @param Corporate $contact
     * @param Job|null  $editJob
     */
    public function __construct(Job $job, $requestType, Corporate $contact, Job $editJob = null)
    {
        $this->creationTime = new DateTime();
        $this->contact = $contact;
        $this->handled = false;

        $this->job = $job;
        $this->setRequestType($requestType);
        $this->editJob = $editJob;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  \BrBundle\Entity\User\Person\Corporate $contact
     * @return \BrBundle\Entity\Company\Request
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return \BrBundle\Entity\User\Person\Corporate
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return \BrBundle\Entity\User\Person\Corporate
     */
    public function handled()
    {
        $this->handled = true;

        return true;
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
     * @param string $type
     */
    private function setRequestType($type)
    {
        if (!in_array($type, self::$possibleRequests)) {
            throw new RuntimeException('The requested type does not exist for the vacancy requests');
        }

        $this->requestType = $type;
    }

    /**
     * @return null
     */
    public function approveRequest()
    {
        switch ($this->requestType) {
            case 'add':
                $this->getJob()->approve();
                break;

            case 'edit':
                $this->getJob()->approve();
                $this->getEditJob()->remove();
                break;

            case 'edit reject':
                $this->getJob()->approve();

                $editJob = $this->getEditJob();
                if ($editJob !== null) {
                    $editJob->remove();
                }

                break;

            default:
                break;
        }
    }

    /**
     * @return null
     */
    public function rejectRequest($message)
    {
        $this->rejectMessage = $message;
    }

    /**
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @return Job
     */
    public function getEditJob()
    {
        return $this->editJob ?? $this->job;
    }

    /**
     * @return string
     */
    public function getRejectMessage()
    {
        return $this->rejectMessage;
    }

    /**
     * @return string
     */
    public function getRequestType()
    {
        return $this->requestType;
    }
}
