<?php

namespace CudiBundle\Entity\Sales;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\ServingQueueItem")
 * @Table(name="cudi.sales_serving_queue_item")
 */
class ServingQueueItem
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;
    
    /**
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;
    
    /**
     * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\ServingQueueStatus")
     * @JoinColumn(name="status", referencedColumnName="id")
     */
    private $status;
    
    /**
     * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\PayDesk")
     * @JoinColumn(name="pay_desk", referencedColumnName="id")
     */
    private $payDesk;
    
    /**
     * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\Session")
     * @JoinColumn(name="sale_session", referencedColumnName="id")
     */
    private $session;
	
    /**
     * @Column(type="smallint")
     */
    private $queueNumber;

    public function __construct() {
    }

    public function getId() {
        return $this->id;
    }

    public function setId( $id_ ) {
        $this->id = $id;
    }

    public function getPerson() {
        return $this->person;
    }

    public function setPerson( $person_ ) {
        $this->person = $person_;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus( $status_ ) {
        $this->status = $status_;
    }

    public function getPayDesk() {
        return $this->payDesk;
    }

    public function setPayDesk( $payDesk_ ) {
        $this->payDesk = $payDesk_;
    }

    public function getSession() {
        return $this->session;
    }

    public function setSession( $session_ ) {
        $this->session = $session_;
    }

    public function getQueueNumber() {
        return $this->queueNumber;
    }

    public function setQueueNumber( $queueNumber_ ) {
        $this->queueNumber = $queueNumber_;
    }
}
