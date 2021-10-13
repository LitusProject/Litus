<?php

namespace SyllabusBundle\Controller;

use CommonBundle\Component\Util\AcademicYear;
use Laminas\View\Model\ViewModel;

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
            ->findAllSubjectsByNameQuery($this->getParam('string'), $academicYear)
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
