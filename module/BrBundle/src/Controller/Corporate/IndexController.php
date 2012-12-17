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
    Zend\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class IndexController extends \BrBundle\Component\Controller\CorporateController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function cvAction()
    {
        $academicYear = $this->getAcademicYear();

        $studies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findAllStudies();

        $result = array();
        foreach ($studies as $study) {

            $parent = $study;
            while ($parent->getParent() !== null)
                $parent = $parent->getParent();

            $entries = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Cv\Entry')
                ->findAllByStudyAndAcademicYear($study, $academicYear);

            if (count($entries) > 0) {
                if (!isset($result[$parent->getId()])) {
                    $result[$parent->getId()] = array(
                        'study' => $parent,
                        'entries' => $entries,
                    );
                } else {
                    $result[$parent->getId()]['entries'] = array_merge($result[$parent->getId()]['entries'], $entries);
                }
            }

        }

        return new ViewModel(
            array(
                'academicYear' => $academicYear,
                'studies' => $result,
            )
        );
    }
}