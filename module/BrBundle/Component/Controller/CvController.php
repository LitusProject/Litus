<?php

namespace BrBundle\Component\Controller;

use CommonBundle\Component\Util\AcademicYear;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CvController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    /**
     * Returns the current academic year.
     *
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    protected function getAcademicYear()
    {
        $date = null;
        if ($this->getParam('academicyear') !== null) {
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        }

        return AcademicYear::getUniversityYear($this->getEntityManager(), $date);
    }
}
