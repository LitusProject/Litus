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

namespace CudiBundle\Form\Admin\Sales\Article;

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
            'name'       => 'academic_year',
            'label'      => 'Academic Year',
            'required'   => true,
            'attributes' => array(
                'options' => $this->getAcademicYears(),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'semester',
            'label'      => 'Semester',
            'required'   => true,
            'attributes' => array(
                'options' => array(
                    '0' => 'All',
                    '1' => 'Semester 1',
                    '2' => 'Semester 2',
                    '3' => 'Semester 1 & 2',
                ),
            ),
        ));

        $this->addSubmit('Download', 'download');
    }

    private function getAcademicYears()
    {
        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $academicYearsArray = array();
        foreach ($academicYears as $academicYear) {
            $academicYearsArray[$academicYear->getId()] = $academicYear->getCode();
        }

        return $academicYearsArray;
    }
}
