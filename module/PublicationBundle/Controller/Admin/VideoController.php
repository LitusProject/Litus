<?php

namespace PublicationBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use PublicationBundle\Entity\Video;

/**
 * VideoController
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class VideoController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('PublicationBundle\Entity\Video')
                ->findAllByDate(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('publication_video_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $video = $form->hydrateObject();

                $this->getEntityManager()->persist($video);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The video was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'publication_admin_video',
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
        $video = $this->getVideoEntity();
        if ($video === null) {
            return new ViewModel();
        }

        $form = $this->getForm('publication_video_edit', array('video' => $video));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The video was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'publication_admin_video',
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

        $video = $this->getVideoEntity();
        if ($video === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($video);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Video|null
     */
    private function getVideoEntity()
    {
        $video = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Video')
            ->findOneById($this->getParam('id'));

        if (!($video instanceof Video)) {
            $this->flashMessenger()->error(
                'Error',
                'No video was found!'
            );

            $this->redirect()->toRoute(
                'publication_admin_video',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $video;
    }
}
