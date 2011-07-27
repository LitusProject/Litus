<?php

namespace Litus\Entities\Syllabus;

/**
 * @Entity(repositoryClass="Litus\Repositories\Syllabus\StudySubjectMapRepository")
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
	 * @OneToOne(targetEntity="Litus\Entities\Syllabus\Study")
	 * @JoinColumn(name="study_id", referencedColumnName="id")
	 */
	private $study;

	/**
	 * @OneToOne(targetEntity="Litus\Entities\Syllabus\Subject")
	 * @JoinColumn(name="subject_id", referencedColumnName="id")
	 */
	private $subject;

    /**
     * @Column(type="boolean")
     */
    private $mandatory;
}
