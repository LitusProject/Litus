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

namespace ShiftBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear,
    Zend\View\Model\ViewModel;

/**
 * CounterController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RankingController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function indexAction()
    {
        $academicYear = $this->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $rankingCriteria = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.ranking_criteria')
        );

        $ranking = array();

        for ($i = 0; isset($rankingCriteria[$i]); $i++) {
            if ($i != (count($rankingCriteria) - 1)) {
                $volunteers = $this->getEntityManager()
                    ->getRepository('ShiftBundle\Entity\Shift\Volunteer')
                    ->findAllByCountLimits($academicYear, $rankingCriteria[$i]['limit'], $rankingCriteria[$i+1]['limit']);
            } else {
                $volunteers = $this->getEntityManager()
                    ->getRepository('ShiftBundle\Entity\Shift\Volunteer')
                    ->findAllByCountMinimum($academicYear, $rankingCriteria[$i]['limit']);
            }

            foreach ($volunteers as $volunteer) {
                $person = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($volunteer['id']);

                if (!$person->isPraesidium($academicYear)) {
                    $ranking[$rankingCriteria[$i]['name']][] = array(
                        'person' => $person,
                        'shiftCount' => $volunteer['shiftCount'],
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'activeAcademicYear' => $academicYear,
                'academicYears' => $academicYears,
                'ranking' => $ranking,
            )
        );
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear|null
     */
    private function getAcademicYear()
    {
        $date = null;
        if (null !== $this->getParam('academicyear')) {
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        }
        $academicYear = AcademicYear::getOrganizationYear($this->getEntityManager(), $date);

        if (null === $academicYear) {
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'shift_admin_shift_counter',
                array(
                    'action' => 'index',
                )
            );

            return;
        }

        return $academicYear;
    }
}
