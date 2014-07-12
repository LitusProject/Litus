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

namespace PageBundle\Controller\Admin;

use PageBundle\Entity\Category,
    PageBundle\Entity\Category\Translation,
    PageBundle\Form\Admin\Category\Add as AddForm,
    PageBundle\Form\Admin\Category\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * CategoryController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class CategoryController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'PageBundle\Entity\Category',
            $this->getParam('page'),
            array(
                'active' => true
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(false),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('page_category_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $category = $form->hydrateObject();

                $this->getEntityManager()->persist($category);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The category was successfully added!'
                );

                $this->redirect()->toRoute(
                    'page_admin_category',
                    array(
                        'action' => 'manage'
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
        if (!($category = $this->_getCategory()))
            return new ViewModel();

        $form = $this->getForm('page_category_edit', $category);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The category was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'page_admin_category',
                    array(
                        'action' => 'manage'
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

        if (!($category = $this->_getCategory()))
            return new ViewModel();

        $category->deactivate();

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                )
            )
        );
    }

    private function _getCategory()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the category!'
            );

            $this->redirect()->toRoute(
                'page_admin_category',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $category = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findOneById($this->getParam('id'));

        if (null === $category) {
            $this->flashMessenger()->error(
                'Error',
                'No category with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'page_admin_category',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $category;
    }
}
