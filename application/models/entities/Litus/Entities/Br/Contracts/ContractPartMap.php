<?php

namespace Litus\Entities\Br\Contracts;

/**
 * @Entity(repositoryClass="Litus\Repositories\Br\Contracts\ContractPartMapRepository")
 * @Table(name="br.contract_part_mapping")
 */
class ContractPartMap {

    /**
     * @Id
     * @ManyToOne(targetEntity="Litus\Entities\Br\Contracts\Contract", cascade={"all"}, fetch="EAGER", inversedBy="parts")
     *
     * @var \Litus\Entities\Br\Contracts\Contract
     */
    private $contract;

    /**
     * @ManyToOne(targetEntity="Litus\Entities\Br\Contracts\Part", fetch="EAGER")
     * @JoinColumn(name="part_id", referencedColumnName="name", onUpdate="CASCADE", onDelete="CASCADE", nullable="false")
     *
     * @var \Litus\Entities\Br\Contracts\Part
     */
    // ManyToOne without a corresponding OneToMany requires a JoinColumn
    private $part;

    /**
     * @Id
     * @Column(name="order_no", type="integer", nullable="false")
     *
     * @var int
     */
    // order is a reserved name in postgres
    private $order;

    public function __construct(Contract $contract, Part $part, $order)
    {
        if($contract === null)
            throw new \Exception('Contract can\'t be null.');

        if($part === null)
            throw new \Exception('Part can\'t be null.');

        if(!is_numeric($order) || ($order <= 0))
            throw new \Exception('$order must be a positive number, not ' . $order);

        $this->contract = $contract;
        $this->part = $part;
        $this->order = $order;
    }

    public function getContract()
    {
        return $this->contract;
    }

    public function getPart()
    {
        return $this->part;
    }

    public function getOrder()
    {
        return $this->order;
    }

}
