<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CommonBundle\Controller;

use Zend\View\Model\ViewModel;

/**
 * SessionController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SessionController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function manageAction()
    {
        $activeSessions = array();
        if ($this->getAuthentication()->isAuthenticated()) {
            $activeSessions = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Session')
                ->findAllActiveByPerson($this->getAuthentication()->getPersonObject());
        }

        $currentSession = $this->getAuthentication()->getSessionObject();

        return new ViewModel(
            array(
                'activeSessions' => $activeSessions,
                'currentSession' => $currentSession,
            )
        );
    }

    public function expireAction()
    {
        $this->initAjax();

        $session = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Session')
            ->findOneById($this->getParam('id'));

        $status = 'error';

        if (null !== $session && $session !== $this->getAuthentication()->getSessionObject()) {
            $session->deactivate();
            $this->getEntityManager()->flush();

            $status = 'success';
        }

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => $status
                )
            )
        );
    }
}
