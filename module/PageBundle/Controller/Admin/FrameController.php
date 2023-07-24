<?php

namespace PageBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use PageBundle\Entity\Frame;
use PageBundle\Entity\Link;
use PageBundle\Entity\Node\CategoryPage;
use PageBundle\Entity\Node\Page;

/**
 * FrameController
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class FrameController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $category_page = $this->getCategoryPageEntity();

        if (!$category_page->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            return $this->notFoundAction();
        }

        $frames = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Frame')
            ->findAllByCategoryPage($category_page);

        $result = array();
        foreach ($frames as $frame) {
            $frame_data = array();
            $frame_data['frame'] = $frame;
            if ($frame->isBig()) {
                $frame_data['frame_type'] = 'Big Frame';
            } else if ($frame->hasDescription()) {
                $frame_data['frame_type'] = 'Small Frame with Description';
            } else if ($frame->hasPoster()) {
                $frame_data['frame_type'] = 'Small Frame with Poster';
            }
            if ($frame->getLinkTo() instanceof Page) {
                $frame_data['linkto_type'] = 'page';
            } else if ($frame->getLinkTo() instanceof Link) {
                $frame_data['linkto_type'] = 'link';
            }

            $result[] = $frame_data;
        }
        $paginator = $this->paginator()->createFromArray(
            $result,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(false),
                'category_page_id' => $category_page->getId(),
            )
        );
    }

    public function addAction()
    {
        $category_page = $this->getCategoryPageEntity();
        $form = $this->getForm('page_frame_add', array('categoryPage' => $category_page));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $frame = $form->hydrateObject();
                $frame->setCategoryPage($category_page);

                $this->getEntityManager()->persist($frame);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The frame was successfully added!'
                );

                $this->redirect()->toRoute(
                    'page_admin_categorypage_frame',
                    array(
                        'action' => 'manage',
                        'category_page_id' => $category_page->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'category_page_id' => $category_page->getId(),
            )
        );
    }

    public function editAction()
    {
        $category_page = $this->getCategoryPageEntity();
        $frame = $this->getFrameEntity();
        if ($category_page === null or $frame === null) {
            return new ViewModel();
        }

        $form = $this->getForm('page_frame_edit', array('category_page' => $category_page));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if (isset($formData['submit']) && $form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The frame was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'page_admin_categorypage_frame',
                    array(
                        'action' => 'manage',
                        'category_page_id' => $category_page->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'frame' => $frame,
                'category_page_id' => $category_page->getId(),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $frame = $this->getFrameEntity();
        if ($frame === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($frame);
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
     * @return CategoryPage|null
     */
    private function getCategoryPageEntity()
    {
        $page = $this->getEntityById('PageBundle\Entity\Node\CategoryPage', 'category_page_id');

        if (!($page instanceof CategoryPage) || !$page->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'No categorypage was found!'
            );

            $this->redirect()->toRoute(
                'page_admin_categorypage',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $page;
    }

    /**
     * @return Frame|null
     */
    private function getFrameEntity()
    {
        $frame = $this->getEntityById('PageBundle\Entity\Frame', 'frame_id');

        if (!($frame instanceof Frame)) {
            $this->flashMessenger()->error(
                'Error',
                'No frame was found!'
            );

            $this->redirect()->toRoute(
                'page_admin_categorypage_frame',
                array(
                    'action' => 'manage',
                    'category_page_id' => $this->getCategoryPageEntity()->getId(),
                )
            );

            return;
        }

        return $frame;
    }
}
