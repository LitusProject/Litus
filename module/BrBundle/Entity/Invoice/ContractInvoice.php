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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Entity\Invoice;

use BrBundle\Entity\Product\Order;
use DateInterval;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Invoice\ContractInvoice")
 * @ORM\Table(name="br_invoices_contract")
 */
class ContractInvoice extends \BrBundle\Entity\Invoice
{
    /**
     * @var Order The order for which this invoice is meant
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Product\Order")
     * @ORM\JoinColumn(name="product_order", referencedColumnName="id")
     */
    private $order;

    /**
     * @var ArrayCollection The entries in this invoice
     *
     * @ORM\OneToMany(
     *      targetEntity="BrBundle\Entity\Invoice\InvoiceEntry",
     *      mappedBy="invoice",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $invoiceEntries;

    public function __construct(Order $order, EntityManager $entityManager)
    {
        parent::__construct($entityManager);

        $this->invoiceEntries = new ArrayCollection();

        $this->setOrder($order);
    }

    public function hasContract()
    {
        return true;
    }

    public function getCompany()
    {
        return $this->getOrder()->getCompany();
    }

    /**
     * @return \DateTime
     */
    public function getExpirationTime()
    {
        $expireTime = 'P' . $this->getOrder()->getContract()->getPaymentDays() . 'D';

        return $this->getCreationTime()->add(new DateInterval($expireTime));
    }

    public function getAuthor()
    {
        return $this->getOrder()->getContract()->getAuthor();
    }

    public function getTitle()
    {
        return $this->getOrder()->getContract()->getTitle();
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param  Order $order
     * @return self
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return array
     */
    public function getAllEntries()
    {
        return $this->invoiceEntries->toArray();
    }

    /**
     * @return array
     * @note    Only the most recent entries get returned in the array.
     */
    public function getEntries()
    {
        $version = $this->getVersion();

        $array = array();

        foreach ($this->getAllEntries() as $entry) {
            if ($entry->getVersion() == $version) {
                array_push($array, $entry);
            }
        }

        return $array;
    }
}
