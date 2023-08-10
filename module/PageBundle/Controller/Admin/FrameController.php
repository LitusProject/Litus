<?php

namespace PageBundle\Controller\Admin;

use Imagick;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use PageBundle\Entity\CategoryPage;
use PageBundle\Entity\Frame;
use PageBundle\Entity\Link;
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

        $big_frames = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Frame')
            ->findAllBigFrames($category_page)
            ->getResult();
        $small_frames = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Frame')
            ->findAllSmallFrames($category_page)
            ->getResult();

        $result = array();
        foreach ($big_frames as $frame) {
            $result[] = $frame;
        }

        foreach ($small_frames as $frame) {
            $result[] = $frame;
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
        $form = $this->getForm('page_frame_add');

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

        $form = $this->getForm('page_frame_edit', $frame);

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

    public function editPosterAction()
    {
        $category_page = $this->getCategoryPageEntity();
        $frame = $this->getFrameEntity();
        if ($category_page === null or $frame === null) {
            return new ViewModel();
        }

        $form = $this->getForm('page_frame_poster');
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'page_admin_categorypage_frame',
                array(
                    'action' => 'upload',
                    'frame_id'     => $frame->getId(),
                    'category_page_id' => $category_page->getId(),
                )
            )
        );

        return new ViewModel(
            array(
                'frame' => $frame,
                'category_page_id' => $category_page->getId(),
                'form'  => $form,
            )
        );
    }

    public function uploadAction()
    {
        $frame = $this->getFrameEntity();
        if ($frame === null) {
            return new ViewModel();
        }

        $form = $this->getForm('page_frame_poster');

        if ($this->getRequest()->isPost()) {
            $form->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );

            if ($form->isValid()) {
                $formData = $form->getData();

                $this->receive($formData['poster'], $frame);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The frame\'s poster has successfully been updated!'
                );

                $this->redirect()->toRoute(
                    'page_admin_categorypage_frame',
                    array(
                        'action' => 'manage',
                        'category_page_id' => $this->getCategoryPageEntity()->getId(),
                    )
                );
                return new ViewModel();
            } else {
                return new ViewModel(
                    array(
                        'status' => 'error',
                        'form'   => array(
                            'errors' => $form->getMessages(),
                        ),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'status' => 'error',
            )
        );
    }

    private function receive($file, Frame $frame)
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('page.frame_poster_path');

        $image = new Imagick($file['tmp_name']);
        $image->thumbnailImage(380, 200, true);

        if ($frame->getPoster() != '' || $frame->getPoster() !== null) {
            $fileName = '/' . $frame->getPoster();
        } else {
            do {
                $fileName = '/' . sha1(uniqid());
            } while (file_exists($filePath . $fileName));
        }

        $image->writeImage($filePath . $fileName);

        $frame->setPoster($fileName);
    }

    /**
     * @return CategoryPage|null
     */
    private function getCategoryPageEntity()
    {
        $page = $this->getEntityById('PageBundle\Entity\CategoryPage', 'category_page_id');

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
