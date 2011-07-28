<?php

namespace Litus\Entities\Br\Contracts;

use Zend\Pdf\Pdf;
use Zend\Pdf\Page;

use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @Entity(repositoryClass="Litus\Repositories\Br\Contracts\ContractRepository")
 * @Table(name="br.contract")
 */
class Contract {

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     *
     * @var int
     */
    private $id;

    /**
     * @OneToMany(targetEntity="Litus\Entities\Br\Contracts\ContractPartMap", mappedBy="contract")
     *
     * @var array
     */
    private $parts;

    private $date;

    private $contactPerson;

    private $company;

    // ...

    public function __construct()
    {
        $this->parts = new ArrayCollection();
    }

    /**
     * Adds the given part at the given order to this Contract.
     *
     * @param Part $part
     * @param int $order
     * @return void
     */
    public function addPart(Part $part, $order)
    {
        $this->parts->add(new ContractPartMap($this, $part, $order));
    }

    /**
     * Returns all the Parts of this contract.
     *
     * @return array
     */
    public function getParts()
    {
        return $this->parts->toArray();
    }
}
