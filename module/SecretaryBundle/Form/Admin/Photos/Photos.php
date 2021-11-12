<?php

namespace SecretaryBundle\Form\Admin\Photos;

/**
 * Form to select the year for downloading promotion photos
 *
 * @author Mathijs Cuppens
 */
class Photos extends \CommonBundle\Component\Form\Admin\Form
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
                    'id'      => 'academic_year',
                    'options' => $this->getAcademicYears(),
                ),
            )
        );

        $this->addSubmit('Download Photos', 'download');
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
