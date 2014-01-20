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

namespace PublicationBundle\Controller\Archive;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

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
        if (!($publication = $this->_getPublication()))
            return new ViewModel();

        $years = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Edition')
            ->findAllYearsByPublication($publication);

        return new ViewModel(
            array(
                'publication' => $publication,
                'years' => $years,
            )
        );
    }

    public function viewAction()
    {
        if (!($publication = $this->_getPublication()))
            return new ViewModel();

        if (!($year = $this->_getYear()))
            return new ViewModel();

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
                'publication' => $publication,
                'year' => $year,
                'pdfs' => $pdfs,
                'htmls' => $htmls,
                'publicPdfDir' => $publicPdfDir,
            )
        );
    }

    private function _getPublication()
    {
        if (null === $this->getParam('publication')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the publication!'
                )
            );

            $this->redirect()->toRoute(
                'publication_archive',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        $publication = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Publication')
            ->findOneById($this->getParam('publication'));

        if (null === $publication) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No publication with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'publication_archive',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        return $publication;
    }

    private function _getYear()
    {
        if (null === $this->getParam('year')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the year!'
                )
            );

            $this->redirect()->toRoute(
                'publication_archive',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        $year = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneById($this->getParam('year'));

        if (null === $year) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No year with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'publication_archive',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        return $year;
    }
}
