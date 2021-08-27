<?php

namespace PageBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use PageBundle\Entity\Link;

/**
 * LinkController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class LinkController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'PageBundle\Entity\Link',
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(false),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('page_link_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $link = $form->hydrateObject();

                $this->getEntityManager()->persist($link);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The link was successfully added!'
                );

                $this->redirect()->toRoute(
                    'page_admin_link',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $link = $this->getLinkEntity();
        if ($link === null) {
            return new ViewModel();
        }

        $form = $this->getForm('page_link_edit', $link);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The link was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'page_admin_link',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $link = $this->getLinkEntity();
        if ($link === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($link);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @return Link|null
     */
    private function getLinkEntity()
    {
        $link = $this->getEntityById('PageBundle\Entity\Link');

        if (!($link instanceof Link)) {
            $this->flashMessenger()->error(
                'Error',
                'No link was found!'
            );

            $this->redirect()->toRoute(
                'page_admin_link',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $link;
    }
}
