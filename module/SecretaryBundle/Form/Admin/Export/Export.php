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

namespace SecretaryBundle\Form\Admin\Export;

use CommonBundle\Component\Form\Admin\Element\Select,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Export form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Export extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Select('organization');
        $field->setLabel('Organiztion')
            ->setAttribute('options', $this->_getOrganizations());
        $this->add($field);

        $field = new Select('academic_year');
        $field->setLabel('Academic Year')
            ->setAttribute('options', $this->_getAcademicYears());
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Download')
            ->setAttribute('class', 'download');
        $this->add($field);
    }

    private function _getOrganizations()
    {
        $organizations = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $organizationsArray = array();
        foreach ($organizations as $organization)
            $organizationsArray[$organization->getId()] = $organization->getName();

        return $organizationsArray;
    }

    private function _getAcademicYears()
    {
        $academicYears = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $academicYearsArray = array();
        foreach ($academicYears as $academicYear)
            $academicYearsArray[$academicYear->getId()] = $academicYear->getCode();

        return $academicYearsArray;
    }
}
