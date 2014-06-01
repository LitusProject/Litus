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

use BrBundle\Entity\Company\Job,
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
 *      "vacanyRequest"="BrBundle\Entity\Company\Request\RequestVacancy"
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
     * @ORM\OneToMany(
     *      targetEntity="BrBundle\Entity\Company\Request",
     *      mappedBy="request")
     */
    //TODO WHY DOES THIS NOT WORK??
    private $coupledRequest;

     /**
     * @var \BrBundle\Entity\User\Person\Corporate The contact used in this order
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\User\Person\Corporate")
     * @ORM\JoinColumn(name="contact", referencedColumnName="id")
     */
    private $contact;

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
    private $coupled;

    /**
     * @var string The type of the request
     *
     * @ORM\Column(type="boolean")
     */
    private $handled;

	public function __construct($contact)
    {
        $this->creationTime = new DateTime();
        $this->contact = $contact;
        $this->handled = false;
        $this->coupled = false;
        $this->coupledRequest = array();
    }

    public function isCoupled()
    {
        return $this->coupled;
    }

    public function setCoupled()
    {
        $this->coupled = true;
    }

    public function addCoupledRequest(Request $request)
    {
        print_r(sizeof($this->coupledRequest));
        array_push($this->coupledRequest, $request);
        print_r(sizeof($this->coupledRequest));
        $request->setCoupled();
    }

    public function getCoupledRequests()
    {
        return $this->coupledRequest;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    public function handled()
    {
        $this->handled = true;
    }

    public abstract function approveRequest();

    public abstract function rejectRequest();
}
