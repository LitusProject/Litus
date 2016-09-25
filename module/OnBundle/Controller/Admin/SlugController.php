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

namespace OnBundle\Controller\Admin;

use OnBundle\Document\Slug,
    Zend\View\Model\ViewModel;

/**
 * SlugController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class SlugController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromDocument(
            'OnBundle\Document\Slug',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'ASC',
            )
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
        $form = $this->getForm('on_slug_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $slug = $form->hydrateObject();

                $this->getDocumentManager()->persist($slug);
                $this->getDocumentManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The slug was successfully created!'
                );

                $this->redirect()->toRoute(
                    'on_admin_slug',
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
        if (!($slug = $this->getSlugEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('on_slug_edit', array('slug' => $slug));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getDocumentManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The slug was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'on_admin_slug',
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

        if (!($slug = $this->getSlugEntity())) {
            return new ViewModel();
        }

        $this->getDocumentManager()->remove($slug);

        $this->getDocumentManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Slug|null
     */
    private function getSlugEntity()
    {
        $slug = $this->getDocumentManager()
            ->getRepository('OnBundle\Document\Slug')
            ->findOneById($this->getParam('id', 0));

        if (!($slug instanceof Slug)) {
            $this->flashMessenger()->error(
                'Error',
                'No slug was found!'
            );

            $this->redirect()->toRoute(
                'on_admin_slug',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $slug;
    }
}
