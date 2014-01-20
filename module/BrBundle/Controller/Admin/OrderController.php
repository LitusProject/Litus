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

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Contract,
    BrBundle\Entity\Contract\ContractEntry,
    BrBundle\Entity\Product\Order,
    BrBundle\Entity\Product\OrderEntry,
    BrBundle\Form\Admin\Order\Add as AddForm,
    BrBundle\Form\Admin\Order\Edit as EditForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * OrderController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class OrderController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\Product\Order',
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager(), $this->getCurrentAcademicYear());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $contact = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\User\Person\Corporate')
                    ->findOneById($formData['contact']);

                $order = new Order(
                    $contact,
                    $this->getAuthentication()->getPersonObject()
                );

                $contract = new Contract($order);

                $products = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Product')
                    ->findByAcademicYear($this->getCurrentAcademicYear());

                $counter = 0;
                foreach ($products as $product)
                {
                    $quantity = $formData['product-' . $product->getId()];
                    if ($quantity != 0)
                    {
                        $orderEntry = new OrderEntry($order, $product, $quantity);
                        $contractEntry = new ContractEntry($contract, $orderEntry, $counter);
                        $counter++;
                        $this->getEntityManager()->persist($orderEntry);
                        $this->getEntityManager()->persist($contractEntry);
                    }
                }

                if ($counter > 0) {
                    $this->getEntityManager()->persist($order);
                    $this->getEntityManager()->persist($contract);
                    $this->getEntityManager()->flush();
                }

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The order was succesfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'br_admin_order',
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
        if (!($order = $this->_getOrder(false)))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $this->getCurrentAcademicYear(), $order);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

                $contact = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\User\Person\Corporate')
                    ->findOneById($formData['contact']);

                $order->setContact($contact);

                // Remove all entries that are no longer needed
                foreach ($order->getEntries() as $entry)
                {
                    $quantity = $formData['product-' . $entry->getProduct()->getId()];
                    if (0 == $quantity)
                    {
                        $this->getEntityManager()->remove($entry);
                    }
                }

                $products = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Product')
                    ->findByAcademicYear($this->getCurrentAcademicYear());

                foreach ($products as $product)
                {
                    $quantity = $formData['product-' . $product->getId()];
                    $orderEntry = $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Product\OrderEntry')
                        ->findOneByOrderAndProduct($order, $product);
                    if (null === $orderEntry && 0 != $quantity)
                    {
                        $orderEntry = new OrderEntry($order, $product, $quantity);
                        $contractEntry = new ContractEntry($order->getContract(), $orderEntry);
                        $this->getEntityManager()->persist($orderEntry);
                        $this->getEntityManager()->persist($contractEntry);
                    } elseif (0 != $quantity) {
                        $orderEntry->setQuantity($quantity);
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The order was succesfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'br_admin_order',
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

        if (!($order = $this->_getOrder()))
            return new ViewModel();

        $this->getEntityManager()->remove($order);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function _getOrder($allowSigned = true)
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the order!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $order = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Product\Order')
            ->findOneById($this->getParam('id'));

        if (null === $order) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No order with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        if ($order->getContract()->isSigned() && !$allowSigned) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'The given order\'s contract has been signed! Signed orders cannot be modified.'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $order;
    }
}
