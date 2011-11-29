<?php

namespace Litus\Entity\Cudi\SaleApp;

use \DateTime;


/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\SaleApp\PollingData")
 * @Table(name="cudi.saleapp_polling_data")
 */
class PollingData
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

    /**
     * @Column(type="datetime")
     */
    private $timestamp;


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

}
