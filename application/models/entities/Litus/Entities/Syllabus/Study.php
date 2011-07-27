<?php

namespace Litus\Entities\Syllabus;

/**
 * @Entity(repositoryClass="Litus\Repositories\Syllabus\StudyRepository")
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
