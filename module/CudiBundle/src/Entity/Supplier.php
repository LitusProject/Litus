<?php

namespace Litus\Entity\Cudi;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Supplier")
 * @Table(name="cudi.supplier")
 */
class Supplier
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
	 * @Column(type="string")
	 */
	private $telephone_number;

	/**
	 * @Column(type="string")
	 */
	private $address;

	/**
	 * @Column(type="string")
	 */
	private $VAT_number;
	
	/**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

	/**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
