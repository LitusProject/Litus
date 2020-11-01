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

namespace BrBundle\Entity\Company\Request;

use BrBundle\Entity\Company\Job;
use BrBundle\Entity\User\Person\Corporate;
use Doctrine\ORM\Mapping as ORM;
use RuntimeException;

/**
 * This is the entity for a student job.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company\Request\StudentJob")
 * @ORM\Table(name="br_companies_requests_student_job")
 */
class StudentJob extends \BrBundle\Entity\Company\Request
{
    /**
     * @var string The type of the request
     *
     * @ORM\Column(type="text")
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
        'delete'      => 'delete',
    );

    /**
     * @param Job       $job
     * @param string    $requestType
     * @param Corporate $contact
     * @param Job|null  $editJob
     */
    public function __construct(Job $job, $requestType, Corporate $contact, Job $editJob = null)
    {
        parent::__construct($contact);

        $this->job = $job;
        $this->setRequestType($requestType);
        $this->editJob = $editJob;
    }

    /**
     * @param string $type
     */
    private function setRequestType($type)
    {
        if (!in_array($type, self::$possibleRequests)) {
            throw new RuntimeException('The requested type does not exist for the student job requests');
        }

        $this->requestType = $type;
    }

    /**
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @return string
     */
    public function getRejectMessage()
    {
        return $this->rejectMessage;
    }

    /**
     * @return Job
     */
    public function getEditJob()
    {
        return $this->editJob;
    }

    /**
     * @return string
     */
    public function getRequestType()
    {
        return $this->requestType;
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

            case 'delete':
                $this->getJob()->remove();
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
}
