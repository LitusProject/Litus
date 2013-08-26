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

namespace BrBundle\Entity;

use BrBundle\Entity\Company,
    BrBundle\Entity\Contract\Composition,
    BrBundle\Entity\Product,
    BrBundle\Entity\Product\Order,
    CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a contract.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Contract")
 * @ORM\Table(name="br.contracts")
 */
class Contract
{
    /**
     * @var int The contract's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \BrBundle\Entity\Product\Order The order for which this contract is meant
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Product\Order")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @var \BrBundle\Entity\Contract\ContractEntry The entries in this contract
     *
     * @ORM\OneToMany(
     *      targetEntity="BrBundle\Entity\Contract\ContractEntry",
     *      mappedBy="contract",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $contractEntries;

    /**
     * @var string The title of the contract
     *
     * @ORM\Column(type="string")
     */
    private $title;

    // TODO: contract number: $entityManager->getRepository('BrBundle\Entity\Contract')->findNextContractNb();
    /**
     * Creates a new contract
     *
     * @param \BrBundle\Entity\Product\Order $order The order to create the contract for.
     */
    public function __construct(Order $order)
    {
        $this->setOrder($order);
        $this->setTitle('Temporary Title - Cannot be changed yet');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return BrBundle\Entity\Product\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param \BrBundle\Entity\Product\Order $order
     * @return \BrBundle\Entity\Contract
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @throws \InvalidArgumentException
     * @param string $title The title of the contract
     * @return \BrBundle\Entity\Br\Contract
     */
    public function setTitle($title)
    {
        if (($title === null) || !is_string($title))
            throw new \InvalidArgumentException('Invalid title');

        $this->title = $title;

        return $this;
    }

    /**
     * @return array
     */
    public function getEntries()
    {
        return $this->contractEntries->toArray();
    }
}
