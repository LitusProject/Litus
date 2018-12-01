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
 *
 * @license http://litus.cc/LICENSE
 */

namespace ApiBundle\Controller;

use CommonBundle\Entity\User\Person;
use Zend\View\Model\ViewModel;

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

        $person = $this->getPersonEntity();
        if ($person === null) {
            return $this->error(404, 'The person was not found');
        }

        $result = array(
            'username'  => $person->getUsername(),
            'full_name' => $person->getFullName(),
            'email'     => $person->getEmail(),
        );

        $academic = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($person->getId());

        if ($academic !== null) {
            $result['university_status'] = $academic->getUniversityStatus($this->getCurrentAcademicYear()) !== null ? $academic->getUniversityStatus($this->getCurrentAcademicYear())->getStatus() : '';
            $result['organization_status'] = $academic->getOrganizationStatus($this->getCurrentAcademicYear(true)) !== null ? $academic->getOrganizationStatus($this->getCurrentAcademicYear(true))->getStatus() : '';
            $result['in_workinggroup'] = $academic->isInWorkingGroup() ?? false;
        }

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
        if ($this->getAccessToken() !== null) {
            return $this->getAccessToken()->getPerson($this->getEntityManager());
        }

        if ($this->getRequest()->getPost('session') !== null) {
            $session = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Session')
                ->findOneById($this->getRequest()->getPost('session'));

            if ($session === null) {
                return null;
            }

            return $session->getPerson();
        }

        if ($this->getRequest()->getPost('username') !== null) {
            return $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person')
                ->findOneByUsername($this->getRequest()->getPost('username'));
        }

        return null;
    }
}
