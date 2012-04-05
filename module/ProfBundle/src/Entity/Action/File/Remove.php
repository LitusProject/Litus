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
 
namespace ProfBundle\Entity\Action\File;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\File;

/**
 * @Entity(repositoryClass="ProfBundle\Repository\Action\File\Remove")
 * @Table(name="prof.action_file_remove")
 */
class Remove extends \ProfBundle\Entity\Action
{
	/**
	 * @var integer The ID of this remove file action
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \CudiBundle\Entity\File The file of this action
	 *
	 * @ManyToOne(targetEntity="CudiBundle\Entity\File")
	 * @JoinColumn(referencedColumnName="id")
	 */
	private $file;
    
    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param \CudiBundle\Entity\File $file
     */
    public function __construct(Person $person, File $file)
    {
        parent::__construct($person);
    	$this->file = $file;
    }
    
    /**
     * @return \CudiBundle\Entity\File
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * @return string
     */
    public function getEntity()
    {
        return 'file';
    }
    
    /**
     * @return string
     */
    public function getAction()
    {
        return 'remove';
    }
}
