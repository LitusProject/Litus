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

namespace PublicationBundle\Controller\Admin;

use PublicationBundle\Entity\Publication,
    Zend\View\Model\ViewModel;

/**
 * PublicationController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class PublicationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('PublicationBundle\Entity\Publication')
                ->findAllActiveQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('publication_publication_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $publication = $form->hydrateObject();

                $this->getEntityManager()->persist($publication);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The publication was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'publication_admin_publication',
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
        if (!($publication = $this->_getPublication())) {
            return new ViewModel();
        }

        $form = $this->getForm('publication_publication_edit', array('publication' => $publication));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The publication was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'publication_admin_publication',
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

        if (!($publication = $this->_getPublication())) {
            return new ViewModel();
        }

        $publication->delete();
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Publication
     */
    private function _getPublication()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the publication!'
            );

            $this->redirect()->toRoute(
                'publication_admin_publication',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $publication = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Publication')
            ->findOneActiveById($this->getParam('id'));

        if (null === $publication) {
            $this->flashMessenger()->error(
                'Error',
                'No publication with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'publication_admin_publication',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $publication;
    }
}
