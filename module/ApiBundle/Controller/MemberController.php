<?php

namespace ApiBundle\Controller;

use Laminas\View\Model\ViewModel;

/**
 * MemberController
 * @author Floris Kint <floris.kint@litus.cc>
 */
class MemberController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function allAction()
    {
        $this->initJson();

        $academicYear = $this->getCurrentAcademicYear();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $result = array();

        $members = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findAllMembers($academicYear);
        foreach ($members as $member) {
            $result[] = (object) array(
                'id'                  => $member->getId(),
                'identification'      => $member->getUniversityIdentification(),
                'firstName'           => $member->getFirstName(),
                'lastName'            => $member->getLastName(),
                'barcode'             => $member->getBarcode() ? $member->getBarcode()->getBarcode() : '',
                'organization_status' => $member->getOrganizationStatus($academicYear) ? $member->getOrganizationStatus($academicYear)->getStatus() : '',
            );
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }
}
