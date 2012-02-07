<?php

namespace SyllabusBundle\Entity;

/**
 * @Entity(repositoryClass="SyllabusBundle\Repository\Study")
 * @Table(name="syllabus.study")
 */
class Study
{
	public function __construct($id,$title,$phase,$type,$acronym,$active,$url)
	{
		$this->id=$id;
		$this->title=$title;
		$this->phase=$phase;
		$this->type=$type;
		$this->acronym=$acronym;
		$this->active=$active;
		$this->url=$url;
		
	}
	
	
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;

    /**
     * @Column(type="string")
     */
    private $title;

    /**
     * @Column(type="smallint")
     */
    private $phase;

    /**
     * @Column(type="boolean")
     */
    private $type;

    /**
     * @Column(type="string")
     */
    private $acronym;

    /**
     * @Column(type="boolean")
     */
    private $active;

    /**
     * @Column(type="string")
     */
    private $url;
}
