<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Entity\Contract;

use BrBundle\Entity\Contract,
    BrBundle\Entity\Product\OrderEntry,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * An entry of a contract.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Contract\ContractEntry")
 * @ORM\Table(name="br.contracts_entries")
 */
class ContractEntry
{

    /**
     * @var int A generated ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \BrBundle\Entity\Contract The contract to which this entry belongs.
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Contract")
     * @ORM\JoinColumn(name="contract_id", referencedColumnName="id")
     */
    private $contract;

    /**
     * @var \BrBundle\Entity\Product\OrderEntry The order entry of which this is an entry in the contract.
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Product\OrderEntry")
     * @ORM\JoinColumn(name="order_entry", referencedColumnName="id")
     */
    private $orderEntry;

    /**
     * @var string The contract text of this product
     *
     * @ORM\Column(name="contract_text", type="text")
     */
    private $contractText;

    /**
     * @param \BrBundle\Entity\Contract $contract The contract of which this entry is part.
     * @param \BrBundle\Entity\Product\OrderEntry $orderEntry The order entry corresponding to this contract entry.
     */
    public function __construct(Contract $contract, OrderEntry $orderEntry)
    {
        $this->contract = $contract;
        $this->orderEntry = $orderEntry;
        $this->contractText = $orderEntry->getProduct()->getContractText();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \BrBundle\Entity\Contract
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @return \BrBundle\Entity\Product\OrderEntry
     */
    public function getOrderEntry()
    {
        return $this->orderEntry;
    }

    /**
     * @return string
     */
    public function getContractText()
    {
        return $this->contractText;
    }

    /**
     * @param string $contractText
     * @return \BrBundle\Entity\Contract\ContractEntry
     */
    public function setContractText($contractText)
    {
        $this->contractText = $contractText;
        return $this;
    }
}
