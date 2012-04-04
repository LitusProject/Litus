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
 
namespace ProfBundle\Entity\Action\Article\Edit;

use ProfBundle\Entity\Action\Article\Edit;

/**
 * @Entity(repositoryClass="ProfBundle\Repository\Action\Article\Edit\Item")
 * @Table(name="prof.action_article_edit_item")
 */
class Item
{
	/**
	 * @var integer The ID of this article edit action item
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \ProfBundle\Entity\Action\Article\Edit The action of this edit item
	 *
	 * @ManyToOne(targetEntity="ProfBundle\Entity\Action\Article\Edit")
	 * @JoinColumn(referencedColumnName="id")
	 */
	private $action;
	
	/**
	 * @var string The field edited
	 *
	 * @Column(type="string")
	 */
	private $field;
	
	/**
	 * @var string The new value
	 *
	 * @Column(type="string")
	 */
	private $value;
    
    /**
     * @param \ProfBundle\Entity\Action\Article\Edit $action
     * @param string $field
     * @param string $value
     */
    public function __construct(Edit $action, $field, $value)
    {
    	$this->action = $action;
    	$this->field = $field;
    	$this->value = $value;
    }
}
