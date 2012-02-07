<?php

namespace CudiBundle\Entity\Sales;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\ServingQueueStatus")
 * @Table(name="cudi.sales_serving_queue_status")
 */
class ServingQueueStatus
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;
	
    /**
     * @Column(type="string")
     */
    private $name;

    public function getId() {
        return $this->id;
    }

    public function setId( $id_ ) {
        $this->id = $id_;
    }

    public function getName() {
        return $this->name;
    }

    public function setName( $name_ ) {
        $this->name = $name_;
    }
}
