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

namespace CudiBundle\Controller\Admin\Sales\Session;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Sales\Session\OpeningHour,
    CudiBundle\Form\Admin\Sales\Session\OpeningHour\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * OpeningHourController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class OpeningHourController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        return new ViewModel();
    }

    public function oldAction()
    {
        return new ViewModel();
    }

    public function addAction()
    {
        return new ViewModel();
    }

    public function editAction()
    {
        return new ViewModel();
    }

    public function deleteAction()
    {
        return new ViewModel();
    }

    private function _getOpeningHour()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the opening hour!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_session',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $openingHour = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Session\OpeningHour')
            ->findOneById($this->getParam('id'));

        if (null === $openingHour) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No opening hour with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_session',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $openingHour;
    }
}