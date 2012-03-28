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
     * @param string $title
     * @param string $subTitle
     * @param integer $phase
     * @param string $language
     */
    public function __construct($title, $subTitle, $phase, $language)
    {
    	$this->title = $title;
    	$this->subTitle = ucfirst(trim(str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $subTitle)));
    	$this->phase = $phase;
    	$this->language = $language;
    	$this->active = true;
    }
    
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * @return string
     */
    public function getSubTitle()
    {
        return $this->subTitle;
    }
    
    /**
     * @return string
     */
    public function getFullTitle()
    {
        return $this->title . ($this->subTitle ? ': ' . $this->subTitle : '');
    }
    
    /**
     * @return integer
     */
    public function getPhase()
    {
        return $this->phase;
    }
    
    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
    
    /**
     * @param boolean $flag
     *
     * @return SyllabusBundle\Entity\Study
     */
    public function setActive($flag = true)
    {
        $this->active = $flag;
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }
}
