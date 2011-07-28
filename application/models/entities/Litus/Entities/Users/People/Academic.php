<?php

namespace Litus\Entities\Users\People;

/**
 * @Entity(repositoryClass="Litus\Repositories\Users\People\Academic")
 * @Table(name="users.people_academics")
 */
abstract class Academic extends \Litus\Entities\Users\Person
{
    /**
	 * @Column(name="personal_email", type="string")
	 */
	private $personalEmail;

	/**
	 * @Column(name="primary_email", type="string")
	 */
	private $primaryEmail;

    /**
	 * @Column(name="university_identification", type="string")
	 */
	private $universityIdentification;

    /**
	 * @Column(name="photo_path", type="string")
	 */
	private $photoPath;

    /**
     * @OneToMany(targetEntity="Litus\Entities\Users\UniversityStatus", mappedBy="person", cascade={"ALL"}, fetch="LAZY")
     */
    private $universityStatuses;

    /**
     * @OneToMany(targetEntity="Litus\Entities\Users\UnionStatus", mappedBy="person", cascade={"ALL"}, fetch="LAZY")
     */
    private $unionStatuses;
}