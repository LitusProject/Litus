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
     * @var BrBundle\Entity\Contract The newest version of the two
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Contract")
     * @ORM\JoinColumn(name="contract", referencedColumnName="id")
     */
    private $contract;

    /**
     * @var BrBundle\Entity\Contract\ContractEntry The oldest version of the two
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
     * @param \CudiBundle\Entity\Article      $article   The new version of the article
     * @param \CudiBundle\Entity\Article|null $precursor The old version of the article
     */
    public function __construct(Contract $contract)
    {
        $this->_setContract($contract);
        $this->_setEntries($contract);
        $this->version = $contract->getVersion();
    }

    public function getContract()
    {
        return $this->contract;
    }

    private function _setContract(Contract $contract)
    {
        $this->contract = $contract;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    private function _setEntries(Contract $contract)
    {
        $this->entries = $contract->getEntries();
    }

    public function getVersion()
    {
        return $this->version;
    }


}
