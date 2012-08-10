<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Entity\Contracts;

use BrBundle\Entity\Contract,
    BrBundle\Entity\Contracts\Section;

/**
 * This entity represents the composition of a contract.
 *
 * @Entity(repositoryClass="BrBundle\Repository\Contracts\Composition")
 * @Table(
 *      name="br.contracts_compositions",
 *      uniqueConstraints={
 *          @UniqueConstraint(name="contract_section_unique", columns={"contract", "section"})
 *      }
 * )
 */
class Composition
{
    /**
     * @var \BrBundle\Entity\Contract The contract this object is a part of
     *
     * @Id
     * @ManyToOne(
     *      targetEntity="BrBundle\Entity\Contract", inversedBy="composition", fetch="EAGER"
     * )
     * @JoinColumn(name="contract", referencedColumnName="id", onDelete="CASCADE")
     */
    private $contract;

    /**
     * @var \BrBundle\Entity\Contracts\Section The section described in this object
     *
     * @ManyToOne(targetEntity="BrBundle\Entity\Contracts\Section", fetch="EAGER")
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
     * @param \BrBundle\Entity\Contract $contract The contract this object is a part of
     * @param \BrBundle\Entity\Contracts\Section $section The section described in this object
     * @param int $position The position number of the section in the contract
     */
    public function __construct(Contract $contract, Section $section, $position)
    {
        $this->setContract($contract);
        $this->setSection($section);
        $this->setPosition($position);
    }

    /**
     * @return \BrBundle\Entity\Contract
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @throws \InvalidArgumentException
     * @param \BrBundle\Entity\Contract $contract
     * @return \BrBundle\Entity\Contracts\Composition
     */
    public function setContract(Contract $contract)
    {
        if ($contract === null)
            throw new \InvalidArgumentException('Contract cannot be null');

        $this->contract = $contract;

        return $this;
    }

    /**
     * @return \BrBundle\Entity\Contracts\Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @throws \InvalidArgumentException
     * @param \BrBundle\Entity\Contracts\Section $section
     * @return \BrBundle\Entity\Contracts\Composition
     */
    public function setSection(Section $section)
    {
        if ($section === null)
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
     * @throws \InvalidArgumentException
     * @param $position int
     * @return \BrBundle\Entity\Contracts\Composition
     */
    public function setPosition($position)
    {
        if ($position < 0)
            throw new \InvalidArgumentException("Position must be a positive number");

        $this->position = round($position);

        return $this;
    }
}
