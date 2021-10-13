<?php

namespace CommonBundle\Controller;

use Laminas\View\Model\ViewModel;

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

        if ($session !== null && $session !== $this->getAuthentication()->getSessionObject()) {
            $session->deactivate();
            $this->getEntityManager()->flush();

            $status = 'success';
        }

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => $status,
                ),
            )
        );
    }
}
