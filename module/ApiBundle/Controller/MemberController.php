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

namespace ApiBundle\Controller;

use Zend\View\Model\ViewModel;

/**
 * MemberController
 * @author Floris Kint <floris.kint@litus.cc>
 */
class MemberController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function allAction()
    {
        $this->initJson();
        if (!($academicYear = $this->getCurrentAcademicYear())) {
            return new ViewModel();
        }

        $result = array();

        $members = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findAllMembers($academicYear);
        foreach ($members as $member) {
            $result[] = (object) array(
                'id' => $member->getId(),
                'identification' => $member->getUniversityIdentification(),
                'firstName' => $member->getFirstName(),
                'lastName' => $member->getLastName(),
                'barcode' => $member->getBarcode() ? $member->getBarcode()->getBarcode() : '',
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
