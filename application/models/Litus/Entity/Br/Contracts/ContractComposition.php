<?php

namespace Litus\Entity\Br\Contracts;

use \Litus\Entity\Br\Contracts\Contract;
use \Litus\Entity\Br\Contracts\Section;

/**
 * @Entity(repositoryClass="Litus\Repository\Br\Contracts\ContractComposition")
 * @Table(
 *      name="br.contract_composition",
 *      uniqueConstraints={
 *          @UniqueConstraint(name="contract_position_unique", columns={"contract", "position"}),
 *          @UniqueConstraint(name="contract_section_unique", columns={"contract", "section"})
 *      }
 * )
 */
class ContractComposition
{
    /**
     * @var \Litus\Entity\Br\Contracts\Contract The contract this object is a part of
     *
     * @ManyToOne(
     *      targetEntity="Litus\Entity\Br\Contracts\Contract", inversedBy="composition", fetch="EAGER"
     * )
     * @JoinColumn(name="contract", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Id
     */
    private $contract;

    /**
     * @var \Litus\Entity\Br\Contracts\Section The section described in this object
     *
     * @ManyToOne(targetEntity="Litus\Entity\Br\Contracts\Section", fetch="EAGER")
     * @JoinColumn(name="section", referencedColumnName="id", onDelete="CASCADE")
     */
    private $section;

    /**
     * @var int The position number of the section in the contract
     *
     * @Id
     * @Column(type="integer")
     */
    private $position;

    /**
     * @param \Litus\Entity\Br\Contracts\Contract $contract The contract this object is a part of
     * @param \Litus\Entity\Br\Contracts\Section $section The section described in this object
     * @param int $position The position number of the section in the contract
     */
    public function __construct(Contract $contract, Section $section, $position)
    {
        $this->setContract($contract);
        $this->setSection($section);
        $this->setPosition($position);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Litus\Entity\Br\Contracts\Contract
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @throws InvalidArgumentException if $contract is null
     * @param \Litus\Entity\Br\Contracts\Contract $contract
     * @return \Litus\Entity\Br\Contracts\ContractComposition
     */
    public function setContract(Contract $contract)
    {
        if($contract === null)
            throw new \InvalidArgumentException('Contract cannot be null');
        $this->contract = $contract;

        return $this;
    }

    /**
     * @return \Litus\Entity\Br\Contracts\Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @throws InvalidArgumentException if $section is null
     * @param \Litus\Entity\Br\Contracts\Section $section
     * @return \Litus\Entity\Br\Contracts\ContractComposition
     */
    public function setSection(Section $section)
    {
        if($section === null)
            throw new \InvalidArgumentException('Contract cannot be null.');
        $this->section = $section;

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
     * @throws \InvalidArgumentException if $position is not a positive integer
     * @param $position int
     * @return \Litus\Entity\Br\Contracts\ContractComposition
     */
    public function setPosition($position)
    {
        if($position < 0)
            throw new \InvalidArgumentException("Position must be a positive number");
        $this->position = round($position);

        return $this;
    }
}
