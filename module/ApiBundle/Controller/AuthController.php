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

use CommonBundle\Entity\User\Person\Academic,
    Zend\View\Model\ViewModel;

/**
 * AuthController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AuthController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function getPersonAction()
    {
        $this->initJson();

        if (null === ($person = $this->_getPerson())) {
            return new ViewModel(
                array(
                    'result' => null
                )
            );
        }

        $result = array(
            'username' => $person->getUsername(),
            'full_name' => $person->getFullName(),
            'email' => $person->getEmail()
        );

        $academic = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($person->getId());

        if (null !== $academic) {
            $result['university_status'] = (null !== $person->getUniversityStatus($this->getCurrentAcademicYear()))
                ? $person->getUniversityStatus($this->getCurrentAcademicYear())->getStatus()
                : '';
            $result['organization_status'] = (null !== $person->getOrganizationStatus($this->getCurrentAcademicYear(true)))
                ? $person->getOrganizationStatus($this->getCurrentAcademicYear(true))->getStatus()
                : '';
        }

        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
    }

    private function _getPerson()
    {
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
