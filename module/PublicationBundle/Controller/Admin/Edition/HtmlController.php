<?php

namespace PublicationBundle\Controller\Admin\Edition;

use DateTime;
use Laminas\View\Model\ViewModel;
use PublicationBundle\Entity\Edition\Html as HtmlEdition;
use PublicationBundle\Entity\Publication;
use ZipArchive;

/**
 * HtmlController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class HtmlController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $publication = $this->getPublicationEntity();
        if ($publication === null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('PublicationBundle\Entity\Edition\Html')
                ->findAllByPublicationQuery($publication, $this->getCurrentAcademicYear()),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'publication'       => $publication,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $publication = $this->getPublicationEntity();
        if ($publication === null) {
            return new ViewModel();
        }

        $form = $this->getForm('publication_edition_html_add', array('publication' => $publication));

        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'publication_admin_edition_html',
                array(
                    'action' => 'upload',
                    'id'     => $publication->getId(),
                )
            )
        );

        return new ViewModel(
            array(
                'publication' => $publication,
                'form'        => $form,
            )
        );
    }

    public function uploadAction()
    {
        $publication = $this->getPublicationEntity();
        if ($publication === null) {
            return new ViewModel();
        }

        $form = $this->getForm('publication_edition_html_add', array('publication' => $publication));
        $formData = $this->getRequest()->getPost();

        $form->setData(
            array_merge_recursive(
                $formData->toArray(),
                $this->getRequest()->getFiles()->toArray()
            )
        );

        $date = self::loadDate($formData['date']);

        if ($form->isValid() && $date) {
            $formData = $form->getData();

            $publicFilePath = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('publication.public_html_directory');
            $filePath = 'public' . $publicFilePath;

            $publicFilePathPdf = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('publication.public_pdf_directory');

            do {
                $fileName = sha1(uniqid());
            } while (file_exists($filePath . $fileName));

            $pdfVersion = $this->getEntityManager()
                ->getRepository('PublicationBundle\Entity\Edition\Pdf')
                ->findOneById($formData['pdf_version']);

            $host = ($this->getRequest()->getServer('HTTPS', 'off') === 'on' ? 'https' : 'http')
                . '://'
                . $this->getRequest()->getServer('HTTP_HOST');
            $html = preg_replace(
                '/{{[ ]*pdfVersion[ ]*}}/',
                $host . $publicFilePathPdf . $pdfVersion->getFileName(),
                preg_replace(
                    '/{{[ ]*imageUrl[ ]*}}/',
                    $host . $publicFilePath . $fileName,
                    $formData['html']
                )
            );

            $edition = new HtmlEdition(
                $publication,
                $this->getCurrentAcademicYear(),
                $formData['title'],
                $html,
                $date,
                $fileName
            );

            if (!file_exists($filePath . $fileName)) {
                mkdir($filePath . $fileName, 0775, true);
            }

            $zipFileName = $formData['file']['tmp_name'];

            $zip = new ZipArchive();

            if ($zip->open($zipFileName) === true) {
                $zip->extractTo($filePath . $fileName);
                $zip->close();
                unlink($zipFileName);
            } else {
                $this->flashMessenger()->error(
                    'Error',
                    'Something went wrong while extracting the archive!'
                );

                return new ViewModel(
                    array(
                        'status' => 'success',
                        'info'   => array(
                            'info' => (object) array(
                                'title' => 'error',
                            ),
                        ),
                    )
                );
            }

            $this->getEntityManager()->persist($edition);
            $this->getEntityManager()->flush();

            $this->flashMessenger()->success(
                'Success',
                'The publication was succesfully created!'
            );

            return new ViewModel(
                array(
                    'status' => 'success',
                    'info'   => array(
                        'info' => (object) array(
                            'title' => $edition->getTitle(),
                        ),
                    ),
                )
            );
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

    public function deleteAction()
    {
        $this->initAjax();

        $edition = $this->getHtmlEditionEntity();
        if ($edition === null) {
            return new ViewModel();
        }

        $publicFilePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('publication.public_html_directory');
        $filePath = 'public' . $publicFilePath;

        if (file_exists($filePath . $edition->getFileName())) {
            $this->rrmdir($filePath . $edition->getFileName());
        }
        $this->getEntityManager()->remove($edition);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return HtmlEdition|null
     */
    private function getHtmlEditionEntity()
    {
        $edition = $this->getEntityById('PublicationBundle\Entity\Edition\Html');

        if (!($edition instanceof HtmlEdition)) {
            $this->flashMessenger()->error(
                'Error',
                'No edition was found!'
            );

            $this->redirect()->toRoute(
                'publication_admin_publication',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $edition;
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

    /**
     * @param string $dir
     */
    private function rrmdir($dir)
    {
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file)) {
                $this->rrmdir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dir);
    }

    /**
     * @param  string $date
     * @return DateTime|null
     */
    private static function loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y', $date) ?: null;
    }
}
