<?php

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
     * @Column(type="integer")
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
}
