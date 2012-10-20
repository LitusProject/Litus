<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
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
            ->findAllActive();

        return new ViewModel(
            array(
                'publications' => $publications,
            )
        );
    }

    public function yearAction()
    {
        return new ViewModel();
    }

    public function viewAction()
    {
        return new ViewModel();
    }

    private function _getPublication()
    {

    }

    private function _getYear()
    {

    }
}
