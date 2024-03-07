<?php

namespace BrBundle\Controller\Corporate;

use BrBundle\Component\Controller\CorporateController;
use Laminas\View\Model\ViewModel;

/**
 * MatchController
 *
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class MatchController extends CorporateController
{
    public function overviewAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        if (!$person->getCompany()->attendsJobfair()) {
            $this->flashMessenger()->error(
                'Error',
                'Your company is not attending this year\'s Jobfair!'
            );
            return new ViewModel();
        }

        $bannerText = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.match_corporate_banner_text');


        $matches = array();
        // TODO Get matches

        $gradesMapEnabled = false;
        $gradesMap = array();
        $entries = array();
        if (!is_null($matches) && in_array($this->getCurrentAcademicYear(), $person->getCompany()->getCvBookYears())) {
            $gradesMapEnabled = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.cv_grades_map_enabled');

            $gradesMap = unserialize(
                $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.cv_grades_map')
            );
            foreach ($matches as $match) {
                $entry = $match->getStudentCV($this->getEntityManager(), $this->getCurrentAcademicYear());
                if ($entry != false) {
                    $entries[] = array('id' => $match->getId(), 'cv' => $entry);
                }
            }
        }


        return new ViewModel(
            array(
                'matches'            => $matches,
                'bannerText'         => $bannerText,
                'academicYear'       => $this->getCurrentAcademicYear()->getCode(),
                'academicYearObject' => $this->getCurrentAcademicYear(),
                'entityManager'      => $this->getEntityManager(),
                'gradesMapEnabled'   => $gradesMapEnabled,
                'gradesMap'          => $gradesMap,
                'profilePath'        => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
                'entries'            => $entries,
                'linkToMatchingSoftware' => 'https://www.vtkjobfair.be/matching-software-companies',
            )
        );
    }
}
