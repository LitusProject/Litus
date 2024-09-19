<?php

namespace BrBundle\Controller\Corporate;

use Laminas\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class IndexController extends \BrBundle\Component\Controller\CorporateController
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
                ->getConfigValue('br.corporate_page_text')
        )[$this->getLanguage()->getAbbrev()];

        return new ViewModel(
            array(
                'members'     => $members,
                'profilePath' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
                'texts'       => $texts,
            )
        );
    }

    public function loginAction()
    {
        return new ViewModel(
            array(
            )
        );
    }

    public function eventsAction()
    {
        $events = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event')
            ->findAllVisibleQuery()->getResult();

        $person = $this->getCorporateEntity();

        if (is_null($person)) {
            foreach ($events as $event) {
                $event->atEvent = false;
            }
        } else {
            $company = $person->getCompany();

            foreach ($events as $event) {
                $companyMap = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Event\CompanyMap')
                    ->findByEventAndCompany($event, $company);

                if ($companyMap === null) {
                    $event->atEvent = false;
                } else {
                    $event->atEvent = true;
                }
            }
        }

        return new ViewModel(
            array(
                'events' => $events,
            )
        );
    }
}
