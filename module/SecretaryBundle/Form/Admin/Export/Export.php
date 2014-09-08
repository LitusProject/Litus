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

/**
 * Export form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Export extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'select',
            'name'       => 'organization',
            'label'      => 'Organization',
            'required'   => true,
            'options'    => array(
                'options' => $this->getOrganizations(),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'academic_year',
            'label'      => 'Academic Year',
            'required'   => true,
            'options'    => array(
                'options' => $this->getAcademicYears(),
            ),
        ));

        $this->addSubmit('Download', 'download');
    }

    private function getOrganizations()
    {
        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $organizationsArray = array();
        foreach ($organizations as $organization)
            $organizationsArray[$organization->getId()] = $organization->getName();

        return $organizationsArray;
    }

    private function getAcademicYears()
    {
        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $academicYearsArray = array();
        foreach ($academicYears as $academicYear)
            $academicYearsArray[$academicYear->getId()] = $academicYear->getCode();

        return $academicYearsArray;
    }
}
