<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\PassKit\Pass;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person,
    CommonBundle\Entity\User\Status\Organization as OrganizationStatus,
    CommonBundle\Component\Util\File\TmpFile,
    Doctrine\ORM\EntityManager,
    ZipArchive;

/**
 * This class can be used to generate a membership pass.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Membership extends \CommonBundle\Component\PassKit\Pass
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \CommonBundle\Entity\User\Person The authenticated person
     */
    private $_authenticatedPerson = null;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The current academic year
     */
    private $_currentAcademicYear = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Component\Util\File\TmpFile $pass The temporary file for the pass
     * @param \CommonBundle\Entity\General\AcademicYear $currentAcademicYear The current academic year
     * @param string $appleRootCertificatePath$this The location of Apple's root certficate
     * @param string $imageDirectory The location of the image directory
     */
    public function __construct(EntityManager $entityManager, Person $authenticatedPerson, AcademicYear $currentAcademicYear, TmpFile $pass, $imageDirectory)
    {
        parent::__construct($pass, $imageDirectory);
        $this->_entityManager = $entityManager;
        $this->_authenticatedPerson = $authenticatedPerson;
        $this->_currentAcademicYear = $currentAcademicYear;

        $this->createPass();
    }

    /**
     * Get the pass' JSON directory.
     *
     * @return string
     */
    protected function getJson()
    {
        $passTypeIdentifiers = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.passkit_pass_type_identifiers');

        $teamIdentifier = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.passkit_team_identifier');

        $organizationName = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('union_name');

        return json_encode(array(
            'formatVersion'      => 1,
            'passTypeIdentifier' => unserialize($passTypeIdentifiers)['membership'],
            'serialNumber'       => uniqid(),
            'teamIdentifier'     => $teamIdentifier,
            'organizationName'   => $organizationName,
            'description'        => $organizationName . ' Membership',
            'foregroundColor'    => 'rgb(255, 255, 255)',
            'backgroundColor'    => 'rgb(34, 50, 90)',
            'generic'            => array(
                'primaryFields' => array(
                    array(
                        'key'   => 'member',
                        'value' => $this->_authenticatedPerson->getFullName(),
                    )
                ),
                'secondaryFields' => array(
                    array(
                        'key'   => 'academicYear',
                        'label' => 'ACADEMIC YEAR',
                        'value' => $this->_currentAcademicYear->getStartDate()->format('Y')
                            . '-'
                            . $this->_currentAcademicYear->getEndDate()->format('Y'),
                    ),
                    array(
                        'key'   => 'status',
                        'label' => 'STATUS',
                        'value' => OrganizationStatus::$possibleStatuses[
                            $this->_authenticatedPerson
                                ->getOrganizationStatus($this->_currentAcademicYear)
                                ->getStatus()
                        ],
                    ),
                ),
            ),
        ));
    }

    /**
     * Get the certicicate used to sign the pass.
     *
     * @return array
     */
    protected function getCertificate()
    {
        $certificatePasswords = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.passkit_certificates');

        return unserialize($certificatePasswords)['membership'];
    }
}
