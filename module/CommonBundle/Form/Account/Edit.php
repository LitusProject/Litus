<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Form\Account;

use Doctrine\ORM\EntityManager,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person\Academic,
    SecretaryBundle\Entity\Organization\MetaData,
    Zend\Cache\Storage\StorageInterface as CacheStorage,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Edit Registration
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \SecretaryBundle\Form\Registration\Edit
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \CommonBundle\Entity\User\Person\Academic     $academic                The academic
     * @param \CommonBundle\Entity\General\AcademicYear     $academicYear            The academic year
     * @param \SecretaryBundle\Entity\Organization\MetaData $metaData                The organization metadata
     * @param \Zend\Cache\Storage\StorageInterface          $cache                   The cache instance
     * @param \Doctrine\ORM\EntityManager                   $entityManager           The EntityManager instance
     * @param string                                        $identification          The university identification
     * @param boolean                                       $enableOtherOrganization Enable the "other organization" option
     * @param null|string|int                               $name                    Optional name for the element
     */
    public function __construct(Academic $academic, AcademicYear $academicYear, MetaData $metaData = null, CacheStorage $cache, EntityManager $entityManager, $identification, $enableOtherOrganization = false, $name = null)
    {
        parent::__construct($academic, $academicYear, $metaData, $cache, $entityManager, $identification, $enableOtherOrganization, $name);

        if (
            null !== $academic->getOrganizationStatus($academicYear)
            && 'praesidium' == $academic->getOrganizationStatus($academicYear)->getStatus()
        ) {
            $this->get('organization')
                ->get('become_member')
                ->setValue(false)
                ->setAttribute('disabled', 'disabled');
        }

        $this->remove('register');

        $field = new Submit('register');
        $field->setValue('Save')
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);

        $this->populateFromAcademic($academic, $academicYear, $metaData);
    }
}
