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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 * @author Hannes Vandecasteele <hannes.vandecasteele@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ApiBundle\Controller;

use CommonBundle\Entity\User\Person,
    CommonBundle\Entity\User\Person\Academic,
    Zend\View\Model\ViewModel;

/**
 * AuthController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AuthController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function personAction()
    {
        $this->initJson();

        if (!($person = $this->getPersonEntity())) {
            return $this->error(404, 'The person was not found');
        }
        print_r("Person found");
        $result = array(
            'username' => $person->getUsername(),
            'full_name' => $person->getFullName(),
            'email' => $person->getEmail(),
        );

        $academic = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($person->getId());

        $corporate = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\User\Person\Corporate')
            ->findOneById($person->getId());

        if (null !== $academic) {
            $result['university_status'] = (null !== $academic->getUniversityStatus($this->getCurrentAcademicYear()))
                ? $academic->getUniversityStatus($this->getCurrentAcademicYear())->getStatus()
                : '';
            $result['organization_status'] = (null !== $academic->getOrganizationStatus($this->getCurrentAcademicYear(true)))
                ? $academic->getOrganizationStatus($this->getCurrentAcademicYear(true))->getStatus()
                : '';
            $result['in_workinggroup'] = (null !== $academic->isInWorkingGroup())
                ? $academic->isInWorkingGroup()
                : false;
        }

        print_r($corporate);
        print_r($corporate->getCompany());
        if (null !== $corporate) {
            print_r("This is a corporate account");
            $result['corporate_id'] = (null !== $corporate->getCompany())
                ? $academic->getCompany()->getId()
                : "-1";
        }

        print_r($result);
        return new ViewModel(
            array(
                'result' => (object) $result,
            )
        );
    }

    public function getPersonAction()
    {
        return $this->personAction();
    }

    /**
     * @return Person|null
     */
    private function getPersonEntity()
    {
        if (null !== $this->getAccessToken()) {
            return $this->getAccessToken()->getPerson($this->getEntityManager());
        }

        if (null !== $this->getRequest()->getPost('session')) {
            $session = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Session')
                ->findOneById($this->getRequest()->getPost('session'));

            return $session->getPerson();
        }

        if (null !== $this->getRequest()->getPost('username')) {
            return $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person')
                ->findOneByUsername($this->getRequest()->getPost('username'));
        }

        return null;
    }
}
