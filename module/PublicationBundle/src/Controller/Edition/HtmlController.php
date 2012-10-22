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

namespace PublicationBundle\Controller\Edition;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    PublicationBundle\Entity\Publication,
    PublicationBundle\Entity\Editions\Html as HtmlEdition,
    PublicationBundle\Form\Admin\Edition\Html\Add as AddForm,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\Validator\File\Size as SizeValidator,
    Zend\Validator\File\Extension as ExtensionValidator,
    Zend\View\Model\ViewModel,
    \ZipArchive;

/**
 * HtmlController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class HtmlController extends \CommonBundle\Component\Controller\ActionController\SiteController
{

    public function viewAction()
    {
        $edition = $this->_getEdition();

        if (!$edition) {
            return new ViewModel();
        }

        return new ViewModel(
            array(
                'edition' => $edition
            )
        );
    }

    private function _getEdition()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the edition!'
                )
            );

            $this->redirect()->toRoute(
                'archive',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        $edition = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Editions\Html')
            ->findOneById($this->getParam('id'));

        if (null === $edition) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No edition with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'archive',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        return $edition;
    }
}