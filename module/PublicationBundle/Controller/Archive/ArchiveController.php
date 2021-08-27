<?php

namespace PublicationBundle\Controller\Archive;

use CommonBundle\Entity\General\AcademicYear;
use Laminas\View\Model\ViewModel;
use PublicationBundle\Entity\Publication;

/**
 * IndexController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class ArchiveController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function overviewAction()
    {
        $publications = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Publication')
            ->findAllActiveWithEdition();

        return new ViewModel(
            array(
                'publications' => $publications,
            )
        );
    }

    public function yearAction()
    {
        $publication = $this->getPublicationEntity();
        if ($publication === null) {
            return new ViewModel();
        }

        $years = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Edition')
            ->findAllYearsByPublication($publication);

        return new ViewModel(
            array(
                'publication' => $publication,
                'years'       => $years,
            )
        );
    }

    public function viewAction()
    {
        $publication = $this->getPublicationEntity();
        if ($publication === null) {
            return new ViewModel();
        }

        $year = $this->getAcademicYearEntity();
        if ($year === null) {
            return new ViewModel();
        }

        $pdfs = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Edition\Pdf')
            ->findAllByPublicationAndAcademicYear($publication, $year);

        $htmls = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Edition\Html')
            ->findAllByPublicationAndAcademicYear($publication, $year);

        $publicPdfDir = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('publication.public_pdf_directory');

        return new ViewModel(
            array(
                'publication'  => $publication,
                'year'         => $year,
                'pdfs'         => $pdfs,
                'htmls'        => $htmls,
                'publicPdfDir' => $publicPdfDir,
            )
        );
    }

    /**
     * @return Publication|null
     */
    private function getPublicationEntity()
    {
        $publication = $this->getEntityById('PublicationBundle\Entity\Publication', 'publication');

        if (!($publication instanceof Publication)) {
            $this->flashMessenger()->error(
                'Error',
                'No publication was found!'
            );

            $this->redirect()->toRoute(
                'publication_archive',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $publication;
    }

    /**
     * @return AcademicYear|null
     */
    private function getAcademicYearEntity()
    {
        $year = $this->getEntityById('CommonBundle\Entity\General\AcademicYear', 'year');

        if (!($year instanceof AcademicYear)) {
            $this->flashMessenger()->error(
                'Error',
                'No year was found!'
            );

            $this->redirect()->toRoute(
                'publication_archive',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $year;
    }
}
