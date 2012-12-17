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

use CommonBundle\Component\FlashMessenger\FlashMessage,
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

        $entries = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findAllByAcademicYear($academicYear);

        return new ViewModel(
            array(
                'academicYear' => $academicYear,
                'entries' => $entries,
            )
        );
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