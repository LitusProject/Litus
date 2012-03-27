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
 
namespace SyllabusBundle\Entity;

/**
 * @Entity(repositoryClass="SyllabusBundle\Repository\Study")
 * @Table(name="syllabus.study")
 */
class Study
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
    private $title;
    
    /**
     * @Column(name="sub_title", type="string")
     */
    private $subTitle;

    /**
     * @Column(type="smallint")
     */
    private $phase;
    
    /**
     * @Column(type="string", length=2)
     */
    private $language;

    /**
     * @Column(type="boolean")
     */
    private $active;

    /**
     * @Column(type="string")
     */
    private $url;
    
    public function __construct($title, $subTitle, $phase, $language, $url)
    {
    	$this->title = $title;
    	$this->subTitle = ucfirst(trim(str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $subTitle)));
    	$this->phase = $phase;
    	$this->language = $language;
    	$this->url = $url;
        //echo $title . ($this->subTitle ? ': ' . $this->subTitle : '') . ' - phase ' . $phase . ' - language ' . $language . '<br>'; 
    }
}
