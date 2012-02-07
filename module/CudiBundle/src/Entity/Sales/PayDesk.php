<?php

namespace CudiBundle\Entity\Sales;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\PayDesk")
 * @Table(name="cudi.sales_pay_desk")
 */
class PayDesk
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
	 * @Column(type="boolean")
	 */
	private $active;
}