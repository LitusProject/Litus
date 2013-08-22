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
    BrBundle\Entity\Product,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents the composition of a contract.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Contract\Composition")
 * @ORM\Table(
 *      name="br.contracts_compositions",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="contract_product_unique", columns={"contract", "product"})
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
     * @var \BrBundle\Entity\Product The product described in this object
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Product", fetch="EAGER")
     * @ORM\JoinColumn(name="product", referencedColumnName="id", onDelete="CASCADE")
     */
    private $product;

    /**
     * @var int The position number of the product in the contract
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @param \BrBundle\Entity\Contract $contract The contract this object is a part of
     * @param \BrBundle\Entity\Product $product The product described in this object
     * @param int $position The position number of the product in the contract
     */
    public function __construct(Contract $contract, Section $product, $position)
    {
        $this->setContract($contract);
        $this->setSection($product);
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
     * @return \BrBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @throws \InvalidArgumentException
     * @param \BrBundle\Entity\Product $product
     * @return \BrBundle\Entity\Contract\Composition
     */
    public function setProduct(Product $product)
    {
        if ($product === null)
            throw new \InvalidArgumentException('Contract cannot be null.');

        $this->product = $product;

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
