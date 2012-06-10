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
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\PayDesk")
 * @Table(name="cudi.sales_pay_desk")
 */
class PayDesk
{
	/**
	 * @var integer The ID of the paydesk
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var string The name of the paydesk
	 *
	 * @Column(type="string")
	 */
	private $name;
	
	/**
	 * @var string The code of the paydesk
	 *
	 * @Column(type="string")
	 */
	private $code;
	
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
	
	/**
	 * @return string
	 */
	public function getCode()
	{
	    return $this->code;
	}
}