<?php

namespace PublicationBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use PublicationBundle\Entity\Publication;

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
                'paginator'         => $paginator,
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
        $publication = $this->getPublicationEntity();
        if ($publication === null) {
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

        $publication = $this->getPublicationEntity();
        if ($publication === null) {
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
     * Processes the uploaded preview image.
     *
     * @param array       $file        The uploaded file info.
     * @param Publication $publication The publication entity.
     */
    private function receivePreview(array $file, Publication $publication)
    {
        // Retrieve the destination path from configuration
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('publication.previeuw_path') . '/';
        
        // Ensure the directory exists
        if (!is_dir($filePath)) {
            mkdir($filePath, 0755, true);
        }
    
        // Generate a unique filename and move the file
        $filename = uniqid() . '-' . basename($file['name']);
        
        if (move_uploaded_file($file['tmp_name'], $filePath . $filename)) {
            $publication->setPreviewImage($filename);
        }
    }


    public function uploadPreviewAction()
    {
        // Retrieve the publication entity (similar to getEventEntity)
        $publication = $this->getPublicationEntity();
        if ($publication === null) {
            return new ViewModel();
        }
    
        // Get the form used for uploading the preview image
        $form = $this->getForm('publication_publication_upload');
        // Set the form action if needed
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'publication_admin_publication',
                array(
                    'action' => 'uploadPreview',
                    'id'     => $publication->getId(),
                )
            )
        );
    
        if ($this->getRequest()->isPost()) {
            // Merge POST and FILE data
            $form->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );
    
            if ($form->isValid()) {
                $data = $form->getData();
    
                // Process the uploaded file (see helper method below)
                $this->receivePreview($data['previewImage'], $publication);
    
                $this->getEntityManager()->flush();
    
                $this->flashMessenger()->success(
                    'Success',
                    'The publication preview image has been updated!'
                );
    
                return new ViewModel(
                    array(
                        'status' => 'success',
                        'info'   => array('name' => $publication->getPreviewImage()),
                    )
                );
            } else {
                return new ViewModel(
                    array(
                        'status' => 'error',
                        'form'   => array('errors' => $form->getMessages()),
                    )
                );
            }
        }
    
        return new ViewModel(array('status' => 'error'));
    }


    /**
     * @return Publication|null
     */
    private function getPublicationEntity()
    {
        $publication = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Publication')
            ->findOneActiveById($this->getParam('id'));

        if (!($publication instanceof Publication)) {
            $this->flashMessenger()->error(
                'Error',
                'No publication was found!'
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
