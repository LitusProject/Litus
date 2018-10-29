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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace PublicationBundle\Controller\Archive;

use CommonBundle\Entity\General\AcademicYear;
use PublicationBundle\Entity\Publication;
use Zend\View\Model\ViewModel;

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
