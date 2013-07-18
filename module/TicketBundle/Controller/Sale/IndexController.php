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

namespace TicketBundle\Controller\Sale;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    TicketBundle\Form\Sale\Ticket\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class IndexController extends \TicketBundle\Component\Controller\SaleController
{
    public function saleAction()
    {
        $event = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Event')
            ->findOneById($this->getParam('id'));

        $form = new AddForm($event);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if ($event->areTicketsGenerated()) {

                } else {

                }
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }
}