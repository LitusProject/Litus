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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Controller;

use CommonBundle\Component\Util\AcademicYear;
use Zend\View\Model\ViewModel;

/**
 * SubjectController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SubjectController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function typeaheadAction()
    {
        $academicYear = $this->getAcademicYear();
        if ($academicYear === null) {
            return $this->notFoundAction();
        }

        $subjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
            ->findAllSubjectsByNameOrCodeQuery($this->getParam('string'), $academicYear)
            ->setMaxResults(20)
            ->getResult();
        $result = array();
        foreach ($subjects as $subjectArr) {
            $subject = (object) $subjectArr;
            $item = (object) array();
            $item->id = $subject->id;
            $item->value = $subject->code . ' - ' . $subject->name;
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear|null
     */
    private function getAcademicYear()
    {
        if ($this->getParam('academicyear') === null) {
            $start = AcademicYear::getStartOfAcademicYear();
        } else {
            $start = AcademicYear::getDateTime($this->getParam('academicyear'));
        }
        $start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($start);

        if ($academicYear === null) {
            return;
        }

        return $academicYear;
    }
}
