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

namespace CommonBundle\Component\ServiceManager;

use CommonBundle\Component\Util\AcademicYear;

/**
 * A trait to define some common methods for classes with a ServiceLocator.
 * This trait requires the class to implement \Zend\ServiceManager\ServiceLocatorAwareInterface.
 *
 * @see \Zend\ServiceManager\ServiceLocatorAwareInterface
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
trait ServiceLocatorAwareTrait
{
    /**
     * We want an easy method to retrieve the DocumentManager from
     * the DI container.
     *
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    public function getDocumentManager()
    {
        return $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
    }

    /**
     * We want an easy method to retrieve the EntityManager from
     * the DI container.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

    /**
     * We want an easy method to retrieve the Cache from
     * the DI container.
     *
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function getCache()
    {
        return $this->getServiceLocator()->get('cache');
    }

    /**
     * Get the current academic year.
     *
     * @param  boolean                                   $organization
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getCurrentAcademicYear($organization = null)
    {
        if (null === $organization) {
            return $this->getServiceLocator()
                ->get('litus.academic_year');
        }

        if ($organization)
            return AcademicYear::getOrganizationYear($this->getEntityManager());

        return AcademicYear::getUniversityYear($this->getEntityManager());
    }

    /**
     * We want an easy method to retrieve the Mail Transport from
     * the DI container.
     *
     * @return \Zend\Mail\Transport\TransportInterface
     */
    public function getMailTransport()
    {
        return $this->getServiceLocator()->get('mail_transport');
    }

    /**
     * Retrieve the common session storage from the DI container.
     *
     * @return \Zend\Session\Container
     */
    public function getSessionStorage()
    {
        return $this->getServiceLocator()->get('common_sessionstorage');
    }

    // Explicitly require the getServiceLocator() method

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    abstract public function getServiceLocator();
}
