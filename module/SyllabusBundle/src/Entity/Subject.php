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
 * @Entity(repositoryClass="SyllabusBundle\Repository\Subject")
 * @Table(name="syllabus.subject")
 */
class Subject
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
    private $code;

    /**
     * @Column(type="string")
     */
    private $name;

    /**
     * @Column(type="smallint")
     */
    private $semester;
    
    /**
     * @Column(type="smallint")
     */
    private $credits;
    
    public function __construct($code, $name, $semester, $credits)
    {
        $this->code = $code;
        $this->name = $name;
        $this->semester = $semester;
        $this->credits = $credits;
        //echo $code . ' - ' . $name . ' - semester ' . $semester . ' - ' . $credits . '<br>'; 
    }
}
