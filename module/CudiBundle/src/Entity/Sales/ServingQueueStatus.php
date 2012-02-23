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

	/**
	 * @return integer
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
