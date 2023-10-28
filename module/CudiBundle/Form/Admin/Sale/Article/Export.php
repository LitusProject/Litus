<?php

namespace CudiBundle\Form\Admin\Sale\Article;

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

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'academic_year',
                'label'      => 'Academic Year',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->getAcademicYears(),
                ),
            )
        );

        $this->add(
            array(
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
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'common',
                'label'      => 'Common items',
                'required'   => true,
            )
        );

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
