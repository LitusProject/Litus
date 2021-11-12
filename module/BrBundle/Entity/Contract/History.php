<?php

namespace BrBundle\Entity\Contract;

use BrBundle\Entity\Contract;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Contract\History")
 * @ORM\Table(name="br_contracts_history")
 */
class History
{
    /**
     * @var integer The ID of this article history
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Contract The newest version of the two
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Contract")
     * @ORM\JoinColumn(name="contract", referencedColumnName="id")
     */
    private $contract;

    /**
     * @var ArrayCollection The oldest version of the two
     *
     * @ORM\OneToMany(targetEntity="BrBundle\Entity\Contract\Entry", mappedBy="contract", cascade={"persist"})
     * @ORM\JoinColumn(name="precursor", referencedColumnName="id")
     */
    private $entries;

    /**
     * @var integer The version of the contract this entry belongs too.
     *
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @param Contract $contract
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
        $this->entries = new ArrayCollection($contract->getEntries());
        $this->version = $contract->getVersion();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Contract
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @return ArrayCollection
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }
}
