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

namespace TicketBundle\Controller\Sale;

use Zend\View\Model\ViewModel;

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

        $form = $this->getForm('ticket_sale_ticket_add', array('event' => $event));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $tickets = $form->hydrateObject($event);
                $formData = $form->getData();

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The tickets were succesfully ' . ($formData['payed'] ? 'sold' : 'booked')
                );

                $this->redirect()->toRoute(
                    'ticket_sale_index',
                    array(
                        'action' => 'sale',
                        'id' => $event->getId(),
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

    public function validateAction()
    {
        $event = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Event')
            ->findOneById($this->getParam('id'));

        $form = $this->getForm('ticket_sale_ticket_add', array('event' => $event));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                return new ViewModel(
                    array(
                        'status' => 'success',
                        'info' => array('status' => 'success'),
                    )
                );
            } else {
                $errors = $form->getMessages();
                $formErrors = array();

                foreach ($form->getElements() as $element) {
                    if (!isset($errors[$element->getName()])) {
                        continue;
                    }

                    $formErrors[$element->getAttribute('id')] = array();

                    foreach ($errors[$element->getName()] as $error) {
                        $formErrors[$element->getAttribute('id')][] = $error;
                    }
                }

                foreach ($form->getFieldSets() as $fieldset) {
                    foreach ($fieldset->getElements() as $subElement) {
                        if (!isset($errors[$fieldset->getName()][$subElement->getName()])) {
                            continue;
                        }

                        $formErrors[$subElement->getAttribute('id')] = array();

                        foreach ($errors[$fieldset->getName()][$subElement->getName()] as $error) {
                            $formErrors[$subElement->getAttribute('id')][] = $error;
                        }
                    }
                }

                return new ViewModel(
                    array(
                        'status' => 'error',
                        'form' => array(
                            'errors' => $formErrors,
                        ),
                    )
                );
            }
        }

        return new ViewModel();
    }
}
