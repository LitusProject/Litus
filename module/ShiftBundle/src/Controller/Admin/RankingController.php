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

namespace ShiftBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    DateInterval,
    DateTime,
    SecretaryBundle\Form\Admin\Registration\Barcode as BarcodeForm,
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
        $academicYear = $this->_getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $volunteers = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shifts\Volunteer')
            ->findAllByAcademicYear($academicYear);

        $volunteersCount = array();
        foreach ($volunteers as $volunteer) {
            if (!$volunteer->getPerson()->isPraesidium($academicYear)) {
                if (!isset($volunteersCount[$volunteer->getPerson()->getId()])) {
                    $person = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Users\People\Academic')
                        ->findOneById($volunteer->getPerson()->getId());

                    $volunteersCount[$volunteer->getPerson()->getId()] = array(
                        'person' => $person,
                        'count' => 0
                    );
                }

                $volunteersCount[$volunteer->getPerson()->getId()]['count']++;
            }
        }

        $rankingCriteria = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.ranking_criteria')
        );

        $ranking = array();
        for ($i = 0; isset($rankingCriteria[$i]); $i++) {
            foreach ($volunteersCount as $volunteerCount) {
                if ($i != (count($rankingCriteria) - 1)) {
                    if ($volunteerCount['count'] >= $rankingCriteria[$i]['limit'] && $volunteerCount['count'] < $rankingCriteria[$i+1]['limit']) {
                        $shiftCount = $this->getEntityManager()
                            ->getRepository('ShiftBundle\Entity\Shift')
                            ->countAllByPerson($volunteerCount['person'], $academicYear);

                        $ranking[$rankingCriteria[$i]['name']][] = array(
                            'person' => $volunteerCount['person'],
                            'shiftCount' => $shiftCount
                        );
                    }
                } else {
                    if ($volunteerCount['count'] >= $rankingCriteria[$i]['limit']) {
                        $shiftCount = $this->getEntityManager()
                            ->getRepository('ShiftBundle\Entity\Shift')
                            ->countAllByPerson($volunteerCount['person'], $academicYear);

                        $ranking[$rankingCriteria[$i]['name']][] = array(
                            'person' => $volunteerCount['person'],
                            'shiftCount' => $shiftCount
                        );
                    }
                }
            }
        }

        return new ViewModel(
            array(
                'activeAcademicYear' => $academicYear,
                'academicYears' => $academicYears,
                'ranking' => $ranking
            )
        );
    }

    private function _getAcademicYear()
    {
        if (null === $this->getParam('academicyear')) {
            $startAcademicYear = AcademicYear::getStartOfAcademicYear();

            $start = new DateTime(
                str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('start_organization_year')
                )
            );

            $next = clone $start;
            $next->add(new DateInterval('P1Y'));
            if ($next <= new DateTime())
                $start = $next;
        } else {
            $startAcademicYear = AcademicYear::getDateTime($this->getParam('academicyear'));

            $start = new DateTime(
                str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('start_organization_year')
                )
            );
        }
        $startAcademicYear->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStart($start);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_shift_counter',
                array(
                    'action' => 'index'
                )
            );

            return;
        }

        return $academicYear;
    }
}
