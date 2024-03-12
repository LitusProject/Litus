<?php

namespace BrBundle\Controller\Career;

use BrBundle\Component\Controller\CareerController;
use CommonBundle\Form\Admin\Unit\Academic;
use Laminas\View\Model\ViewModel;

/**
 * MatchController
 *
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class MatchController extends CareerController
{
    public function overviewAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        if ($person === null) {
            return new ViewModel();
        }

        $matches = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\StudentCompanyMatch')
            ->findAllByStudentAndYear($person, $this->getCurrentAcademicYear());

        $bannerText = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.match_career_banner_text');

        return new ViewModel(
            array(
                'matches'    => $matches,
                'bannerText' => $bannerText,
                'em'         => $this->getEntityManager(),
                'ay'         => $this->getCurrentAcademicYear(),
                'linkToMatchingSoftware' => 'https://www.vtkjobfair.be/matching-software-students',
            )
        );
    }
}
