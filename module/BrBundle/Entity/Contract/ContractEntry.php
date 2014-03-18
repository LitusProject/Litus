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
     * @var int The position number of the entry in the contract
     *
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @var int The version of the contract this entry belongs too.
     *
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @param \BrBundle\Entity\Contract $contract               The contract of which this entry is part.
     * @param \BrBundle\Entity\Product\OrderEntry $orderEntry   The order entry corresponding to this contract entry.
     * @param int $position                                     The position number of the entry in the contract
     * @param int $version                                      The version number of this contract entry
     */
    public function __construct(Contract $contract, OrderEntry $orderEntry, $position, $version)
    {
        $this->contract = $contract;
        $this->orderEntry = $orderEntry;
        $this->setContractText($orderEntry->getProduct()->getContractText());
        $this->setPosition($position);
        $this->_setVersion($version);
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return int
     */
    private function _setVersion($versionNmbr)
    {
        if($versionNmbr < 0)
            throw new \InvalidArgumentException("version number must be larger or equal to zero");
        $this->position = $versionNmbr;
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

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the position to the given value.
     *
     * @throws \InvalidArgumentException
     * @param $position int
     * @return \BrBundle\Entity\Contract\Composition
     */
    public function setPosition($position)
    {
        if ($position < 0)
            throw new \InvalidArgumentException("Position must be a positive number");

        $this->position = round($position);

        return $this;
    }
}
