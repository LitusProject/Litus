<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
  // TODO: comments
 
 
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
