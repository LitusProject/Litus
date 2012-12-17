<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

use CommonBundle\Entity\General\AcademicYear,
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
        $academicYear = $this->getAcademicYear();

        $result = $this->_getGrouped($academicYear);

        return new ViewModel(
            array(
                'academicYear' => $academicYear,
                'studies' => $result,
            )
        );
    }

    public function listAction()
    {
        $academicYear = $this->getAcademicYear();

        $entries = $this->_getList($academicYear);

        return new ViewModel(
            array(
                'academicYear' => $academicYear,
                'entries' => $entries,
            )
        );
    }

    public function searchAction()
    {
        // $this->initAjax();

        $academicYear = $this->getAcademicYear();

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

    private function _getGrouped(AcademicYear $academicYear) {

        $groups = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findAllCvBook();

        $result = array();
        foreach ($groups as $group) {

            $entries = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Cv\Entry')
                ->findAllByGroupAndAcademicYear($group, $academicYear);

            if (count($entries) > 0) {
                $result[] = array(
                    'id' => 'group-' . $group->getId(),
                    'name' => $group->getName(),
                    'entries' => $entries,
                );
            }
        }

        // Add all studies that are not in a cv book group.
        $cvStudies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findAllUngroupedStudies();

        foreach ($cvStudies as $study) {

            $entries = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Cv\Entry')
                ->findAllByStudyAndAcademicYear($study, $academicYear);

            if (count($entries) > 0) {
                $result[] = array(
                    'id' => 'study-' . $study->getId(),
                    'name' => $study->getFullTitle(),
                    'entries' => $entries,
                );
            }

        }

        return $result;
    }

    private function _getList(AcademicYear $academicYear)
    {
        return $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findAllByAcademicYear($academicYear);
    }

    private function _doFilter($entries, $filters) {

        if (isset($filters['string'])) {
            $entries = $this->_filterString($entries, $filters['string']);
        }

        if (isset($filters['grade'])) {
            $entries = $this->_filterGrade($entries, $filters['grade']);
        }

        return $entries;
    }

    private function _filterString($entries, $string) {
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

    private function _filterGrade($entries, $grade) {
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

    public function cvPhotoAction() {
        $imagePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.profile_path') . '/' . $this->getParam('image');

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'inline; filename="' . $this->getParam('image') . '"',
            'Content-type' => mime_content_type($imagePath),
            'Content-Length' => filesize($imagePath),
        ));
        $this->getResponse()->setHeaders($headers);

        $handle = fopen($imagePath, 'r');
        $data = fread($handle, filesize($imagePath));
        fclose($handle);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }
}