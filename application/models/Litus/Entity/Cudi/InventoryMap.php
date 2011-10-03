<?php

namespace Litus\Entity\Cudi;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\InventoryMap")
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
	 * @OneToOne(targetEntity="Litus\Entity\Cudi\Article")
	 * @JoinColumn(name="article", referencedColumnName="id")
	 */
	private $article;

	/**
	 * @OneToOne(targetEntity="Litus\Entity\Users\Person")
	 * @JoinColumn(name="person", referencedColumnName="id")
	 */
	private $person;

    /**
     * @Column(type="integer")
     */
    private $number;
}
