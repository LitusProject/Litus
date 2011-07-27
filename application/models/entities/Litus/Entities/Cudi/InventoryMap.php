<?php

namespace Litus\Entities\Cudi;

/**
 * @Entity(repositoryClass="Litus\Repositories\Cudi\InventoryMapRepository")
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
	 * @OneToOne(targetEntity="Litus\Entities\Cudi\Article")
	 * @JoinColumn(name="article_id", referencedColumnName="id")
	 */
	private $article;

	/**
	 * @OneToOne(targetEntity="Litus\Entities\Users\Person")
	 * @JoinColumn(name="person_id", referencedColumnName="id")
	 */
	private $person;

    /**
     * @Column(type="integer")
     */
    private $number;
}
