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

namespace ApiBundle\Controller;

use CommonBundle\Entity\Users\People\Academic,
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
        if (!($person = $this->_getPerson())) {
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
            ->getRepository('CommonBundle\Entity\Users\People\Academic')
            ->findOneById($person->getId());

        if (null !== $academic)
            $result['university_status'] = $person->getUniversityStatus($this->getCurrentAcademicYear())->getStatus();

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
                ->getRepository('CommonBundle\Entity\Users\Session')
                ->findOneById($this->getRequest()->getPost('session'));

            return $session->getPerson();
        }

        if (null !== $this->getRequest()->getPost('username')) {
            return $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Users\Person')
                ->findOneByUsername($this->getRequest()->getPost('username'));
        }

        return null;
    }
}
