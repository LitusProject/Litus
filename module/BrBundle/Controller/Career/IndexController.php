<?php

namespace BrBundle\Controller\Career;

use Laminas\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class IndexController extends \BrBundle\Component\Controller\CareerController
{
    public function indexAction()
    {
        $academicYear = $this->getCurrentAcademicYear(true);
        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActiveQuery()->getResult();

        $br = null;
        foreach ($units as $unit) {
            if ($unit->getName() === 'Bedrijvenrelaties') {
                $br = $unit;
            }
        }

        $members = array();
        if ($br != null) {
            $members = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
                ->findAllByUnitAndAcademicYear($br, $academicYear);
        }

        $texts = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.career_page_text')
        )[$this->getLanguage()->getAbbrev()];

        return new ViewModel(
            array(
                'members'     => $members,
                'profilePath' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
                'texts'       => $texts,
                'fathom'      => $this->getFathomInfo(),
            )
        );
    }

    public function calendarAction(){
        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllCareerAndActiveAndNotHidden();

        $calendarItems = array();
        foreach ($events as $event) {
            $calendarItems[$event->getId()] = $event;
        }

        return new ViewModel(
            array(
                'entityManager' => $this->getEntityManager(),
                'calendarItems' => $calendarItems,
            )
        );
    }
}
