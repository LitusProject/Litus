<?php

namespace SecretaryBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use SecretaryBundle\Entity\Pull;

class PullController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\Event')
                ->findAllQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    /**
     * To Do: Forms Maken, Repository Maken, Views Maken, Configs, Router
     */
    public function addAction()
    {

    }

    public function editAction()
    {

    }

    public function deleteAction()
    {

    }
}
