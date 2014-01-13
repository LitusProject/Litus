<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Controller\Corporate;

use BrBundle\Entity\Cv\Util,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * CvController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class CvController extends \BrBundle\Component\Controller\CorporateController
{
    public function groupedAction()
    {
        $person = $this->getAuthentication()->getPersonObject();

        if ($person === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'Please login to view the CV book.'
                )
            );

            $this->redirect()->toRoute(
                'br_corporate_index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return new ViewModel();
        }

        $academicYear = $this->getAcademicYear();

        if (!in_array($academicYear, $person->getCompany()->getCvBookYears())) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You don\'t have access to the CVs of this year.'
                )
            );

            $this->redirect()->toRoute(
                'br_corporate_index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return new ViewModel();
        }

        $result = Util::getGrouped($this->getEntityManager(), $academicYear);

        return new ViewModel(
            array(
                'academicYear' => $academicYear,
                'studies' => $result,
            )
        );
    }

    public function listAction()
    {
        $person = $this->getAuthentication()->getPersonObject();

        if ($person === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'Please login to view the CV book.'
                )
            );

            $this->redirect()->toRoute(
                'br_corporate_index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return new ViewModel();
        }

        $academicYear = $this->getAcademicYear();

        if (!in_array($academicYear, $person->getCompany()->getCvBookYears())) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You don\'t have access to the CVs of this year.'
                )
            );

            $this->redirect()->toRoute(
                'br_corporate_index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return new ViewModel();
        }

        $entries = $this->_getList($academicYear);

        return new ViewModel(
            array(
                'academicYear' => $academicYear,
                'entries' => $entries,
                'profilePath' =>$this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $person = $this->getAuthentication()->getPersonObject();

        if ($person === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'Please login to view the CV book.'
                )
            );

            $this->redirect()->toRoute(
                'br_corporate_index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return new ViewModel();
        }

        $academicYear = $this->getAcademicYear();

        if (!in_array($academicYear, $person->getCompany()->getCvBookYears())) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You don\'t have access to the CVs of this year.'
                )
            );

            $this->redirect()->toRoute(
                'br_corporate_index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return new ViewModel();
        }

        $filters = array();

        if (null !== $this->getParam('string'))
            $filters['string'] = $this->getParam('string');

        if (null !== $this->getParam('min') || null !== $this->getParam('max')) {
            $filters['grade'] = array();
            if (null !== $this->getParam('min'))
                $filters['grade']['min'] = $this->getParam('min');
            else
                $filters['grade']['min'] = 0;
            if (null !== $this->getParam('max'))
                $filters['grade']['max'] = $this->getParam('max');
            else
                $filters['grade']['max'] = 100;
        }

        $type = $this->getParam('type');

        $filtered = $this->_doFilter($this->_getList($academicYear), $filters);
        $result = array();
        foreach ($filtered as $entry) {
            $result[] = $entry->getId();
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _getList(AcademicYear $academicYear)
    {
        return $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findAllByAcademicYear($academicYear);
    }

    private function _doFilter($entries, $filters)
    {

        if (isset($filters['string'])) {
            $entries = $this->_filterString($entries, $filters['string']);
        }

        if (isset($filters['grade'])) {
            $entries = $this->_filterGrade($entries, $filters['grade']);
        }

        return $entries;
    }

    private function _filterString($entries, $string)
    {
        $result = array();
        $words = preg_split('/[\s,]+/', $string);
        foreach ($entries as $entry) {
            $matches = true;
            foreach ($words as $word) {
                if (!(preg_match('/.*' . $word . '.*/i', $entry->getLastName()) || preg_match('/.*' . $word . '.*/i', $entry->getFirstName()))) {
                    $matches = false;
                    break;
                }
            }
            if ($matches)
                $result[] = $entry;
        }

        return $result;
    }

    private function _filterGrade($entries, $grade)
    {
        $result = array();
        $min = $grade['min'] * 100;
        $max = $grade['max'] * 100;
        foreach ($entries as $entry) {
            if ($entry->getGrade() >= $min && $entry->getGrade() <= $max) {
                $result[] = $entry;
            }
        }

        return $result;
    }
}
