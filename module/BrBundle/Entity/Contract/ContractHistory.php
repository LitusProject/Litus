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
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Contract\ContractHistory")
 * @ORM\Table(name="br.contract_history")
 */
class ContractHistory
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
     * @ORM\OneToMany(targetEntity="BrBundle\Entity\Contract\ContractEntry", mappedBy="contract", cascade={"persist"})
     * @ORM\JoinColumn(name="precursor", referencedColumnName="id")
     */
    private $entries;

    /**
     * @var int The version of the contract this entry belongs too.
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
     * @return int
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
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
}
