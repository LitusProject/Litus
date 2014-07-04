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
    BrBundle\Entity\Contract\Section,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents the composition of a contract.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Contract\Composition")
 * @ORM\Table(
 *      name="br.contracts_compositions",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="contract_section_unique", columns={"contract", "section"})
 *      }
 * )
 */
class Composition
{
    /**
     * @var \BrBundle\Entity\Contract The contract this object is a part of
     *
     * @ORM\Id
     * @ORM\ManyToOne(
     *      targetEntity="BrBundle\Entity\Contract", inversedBy="composition", fetch="EAGER"
     * )
     * @ORM\JoinColumn(name="contract", referencedColumnName="id", onDelete="CASCADE")
     */
    private $contract;

    /**
     * @var \BrBundle\Entity\Contract\Section The section described in this object
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Contract\Section", fetch="EAGER")
     * @ORM\JoinColumn(name="section", referencedColumnName="id", onDelete="CASCADE")
     */
    private $section;

    /**
     * @var int The position number of the section in the contract
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @param \BrBundle\Entity\Contract         $contract The contract this object is a part of
     * @param \BrBundle\Entity\Contract\Section $section  The section described in this object
     * @param int                               $position The position number of the section in the contract
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
     * @param  \BrBundle\Entity\Contract             $contract
     * @return \BrBundle\Entity\Contract\Composition
     */
    public function setContract(Contract $contract)
    {
        if ($contract === null)
            throw new \InvalidArgumentException('Contract cannot be null');

        $this->contract = $contract;

        return $this;
    }

    /**
     * @return \BrBundle\Entity\Contract\Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @throws \InvalidArgumentException
     * @param  \BrBundle\Entity\Contract\Section     $section
     * @return \BrBundle\Entity\Contract\Composition
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
     * @param  integer                               $position int
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
