<?php

namespace CudiBundle\Entity;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\InventoryMap")
 * @Table(name="cudi.inventory_map")
 */
class InventoryMap
{
    /**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
    private $id;

    /**
	 * @OneToOne(targetEntity="CudiBundle\Entity\Article")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;

	/**
	 * @OneToOne(targetEntity="CommonBundle\Entity\Users\Person")
	 * @JoinColumn(name="person", referencedColumnName="id")
	 */
	private $person;

    /**
     * @Column(type="integer")
     */
    private $number;
}
