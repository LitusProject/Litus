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
	 * @JoinColumn(name="study", referencedColumnName="id")
	 */
	private $study;

	/**
	 * @OneToOne(targetEntity="Litus\Entity\Syllabus\Subject")
	 * @JoinColumn(name="subject", referencedColumnName="id")
	 */
	private $subject;

    /**
     * @Column(type="boolean")
     */
    private $mandatory;
}
