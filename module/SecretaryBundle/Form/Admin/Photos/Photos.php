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

        $this->add(array(
            'type'       => 'select',
            'name'       => 'academic_year',
            'label'      => 'Academic Year',
            'required'   => true,
            'attributes' => array(
                'id'      => 'academic_year',
                'options' => $this->_getAcademicYears(),
            ),
        ));

        $this->addSubmit('Download Photos', 'download');
    }

    private function _getAcademicYears()
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
