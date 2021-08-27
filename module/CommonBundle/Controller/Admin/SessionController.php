<?php

namespace CommonBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;

/**
 * SessionController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class SessionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
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
