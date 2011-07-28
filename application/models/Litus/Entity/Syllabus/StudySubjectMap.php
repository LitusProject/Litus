<?php

namespace Litus\Entity\Syllabus;

/**
 * @Entity(repositoryClass="Litus\Repository\Syllabus\StudySubjectMap")
 * @Table(name="syllabus.study_subject_map")
 */
class StudySubjectMap
{
    /**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
    private $id;

    /**
	 * @OneToOne(targetEntity="Litus\Entity\Syllabus\Study")
	 * @JoinColumn(name="study_id", referencedColumnName="id")
	 */
	private $study;

	/**
	 * @OneToOne(targetEntity="Litus\Entity\Syllabus\Subject")
	 * @JoinColumn(name="subject_id", referencedColumnName="id")
	 */
	private $subject;

    /**
     * @Column(type="boolean")
     */
    private $mandatory;
}
