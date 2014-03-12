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
    private $dirty;

    /**
     * @param \CommonBundle\Entity\User\Person $author   The author of this contract
     * @param \BrBundle\Entity\Contract        $company  The company for which this contract is meant
     * @param int                              $discount The discount associated with this contract
     * @param string                           $title    The title of the contract
     */
    public function __construct(Order $order)
    {
        $this->setOrder($order);
        $this->setTitle('Contract ' . $order->getCompany()->getName());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return \BrBundle\Entity\Br\Contract
     */
    public function setDate()
    {
        $this->date = new DateTime();

        return $this;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @throws \InvalidArgumentException
     * @param  \CommonBundle\Entity\User\Person $author
     * @return \BrBundle\Entity\Br\Contract
     */
    public function setAuthor(Person $author)
    {
        if ($author === null)
            throw new \InvalidArgumentException('Author cannot be null');

        $this->author = $author;

        return $this;
    }

    /**
     * @return BrBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @throws \InvalidArgumentException
     * @param  \BrBundle\Entity\Company     $company
     * @return \BrBundle\Entity\Br\Contract
     */
    public function setCompany(Company $company)
    {
        if ($company === null)
            throw new \InvalidArgumentException('Company cannot be null');

        $this->company = $company;

        return $this;
    }

    /**
     * @return array
     */
    public function getComposition()
    {
        return $this->composition->toArray();
    }

    /**
     * @return \BrBundle\Entity\Br\Contract
     */
    public function resetComposition()
    {
        $this->composition->clear();

        return $this;
    }

    /**
     * @param  \BrBundle\Entity\Br\Contracts\Section $section  The section that should be added
     * @param  int                                   $position The position of this section
     * @return \BrBundle\Entity\Br\Contract
     */
    public function addSection(Section $section, $position)
    {
        $this->composition->add(
            new Composition($this, $section, $position)
        );

        return $this;
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
     * @param  string                       $title The title of the contract
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
     * @return bool
     */
    public function isDirty()
    {
        return $this->dirty;
    }

    /**
     * @return array
     */
    public function getEntries()
    {
        return $this->contractEntries->toArray();
    }

    /**
     * @return bool
     */
    public function isSigned()
    {
        return $this->getInvoiceNb() != -1;
    }

    /**
     * @return int
     */
    public function getInvoiceNb()
    {
        return $this->invoiceNb;
    }

    /**
     * @throws \InvalidArgumentException
     * @param  int                          $invoiceNb
     * @return \BrBundle\Entity\Br\Contract
     */
    public function setInvoiceNb($invoiceNb = -1)
    {
        if (($invoiceNb === null) || !is_numeric($invoiceNb))
            throw new \InvalidArgumentException('Invalid invoice number: ' . $invoiceNb);

        $this->invoiceNb = $invoiceNb;

        return $this;
    }

    /**
     * @return int
     */
    public function getContractNb()
    {
        return $this->contractNb;
    }

    /**
     * @param  int                          $contractNb
     * @return \BrBundle\Entity\Br\Contract
     */
    public function setContractNb($contractNb)
    {
        if(($contractNb === null) || !is_numeric($contractNb))
            throw new \InvalidArgumentException('Invalid contract number: ' . $contractNb);

        $this->contractNb = $contractNb;

        return $this;
    }
}
