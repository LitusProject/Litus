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
    BrBundle\Entity\Contract\Section,
    BrBundle\Entity\Contract\Composition,
    BrBundle\Entity\Product\OrderEntry,
    BrBundle\Form\Admin\Order\Add as AddForm,
    BrBundle\Form\Admin\Order\Edit as EditForm,
    BrBundle\Form\Admin\Order\AddProduct as AddProductForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * OrderController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
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
                'entityManager' => $this->getEntityManager(),
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

                $company = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findOneById($formData['company']);

                if ($formData['tax'] == true) {
                    $tax = true;
                } else
                    $tax = false;

                $order = new Order(
                    $contact,
                    $this->getAuthentication()->getPersonObject(),
                    $tax
                );

                $contract = new Contract($order,
                    $this->getAuthentication()->getPersonObject(),
                    $company,
                    $formData['discount'],
                    $formData['title']
                );

                $contract->setContractNb(
                    $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Contract')
                        ->findNextContractNb()
                );

                $this->getEntityManager()->persist($order);
                $this->getEntityManager()->persist($contract);

                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'br_admin_order',
                    array(
                        'action' => 'product',
                        'id' => $order->getId(),
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

    public function productAction()
    {
        if (!($order = $this->_getOrder(false)))
            return new ViewModel();
        if($order->getContract()->isSigned() == true)
            return new ViewModel();

        $entries = $this->_getOrder(false)->getEntries();

        $oldContract = $order->getContract();

        $currentProducts = array();
        foreach ($entries as $entry) {
            array_push($currentProducts, $entry->getProduct());
        }
        $form = new AddProductForm($currentProducts, $this->getEntityManager(), $this->getCurrentAcademicYear());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $updatedOrder = new Order(
                    $order->getContact(),
                    $this->getAuthentication()->getPersonObject(),
                    $order->isTaxFree()
                );

                $contract = new Contract($updatedOrder,
                    $this->getAuthentication()->getPersonObject(),
                    $oldContract->getCompany(),
                    $oldContract->getDiscount(),
                    $oldContract->getTitle()
                );

                $contract->setContractNb(
                    $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Contract')
                        ->findNextContractNb()
                );

                $counter = 0;

                foreach ($entries as $entry) {
                    $orderEntry = new OrderEntry($updatedOrder, $entry->getProduct(), $entry->getQuantity());
                    $contractEntry = new ContractEntry($contract, $orderEntry, $counter,0);
                    $order->setEntry($orderEntry);
                    $contract->setEntry($contractEntry);
                    $counter++;
                    $this->getEntityManager()->persist($orderEntry);
                    $this->getEntityManager()->persist($contractEntry);
                }

                $newProduct = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Product')
                    ->findProductById($formData['product']);

                $orderEntry = new OrderEntry($updatedOrder, $newProduct[0], $formData['amount']);
                $contractEntry = new ContractEntry($contract, $orderEntry, $counter,0);
                $order->setEntry($orderEntry);
                $contract->setEntry($contractEntry);

                $this->getEntityManager()->persist($orderEntry);
                $this->getEntityManager()->persist($contractEntry);
                $this->getEntityManager()->persist($updatedOrder);
                $this->getEntityManager()->persist($contract);

                $order->setOld();

                $this->getEntityManager()->persist($order);

                $this->getEntityManager()->flush();


                $this->redirect()->toRoute(
                    'br_admin_order',
                    array(
                        'action' => 'product',
                        'id' => $updatedOrder->getId(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'entries' => $entries,
                'addProductForm' => $form,
            )
        );
   }

    public function editAction()
    {
        if (!($order = $this->_getOrder(false)))
            return new ViewModel();
        if($order->getContract()->isSigned() == true)
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

                if ($formData['tax'] == true) {
                    $tax = true;
                } else
                    $tax = false;

                $updatedOrder = new Order(
                    $contact,
                    $this->getAuthentication()->getPersonObject(),
                    $tax
                );

                $updatedContract = new Contract($updatedOrder,
                    $this->getAuthentication()->getPersonObject(),
                    $order->getCompany(),
                    $formData['discount'],
                    $formData['title']
                );

                $updatedContract->setContractNb(
                    $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Contract')
                        ->findNextContractNb()
                );

                $entries = $order->getEntries();

                $counter = 0;
                foreach ($entries as $entry)
                {
                    $updatedOrderEntry = new OrderEntry($updatedOrder, $entry->getProduct(), $entry->getQuantity());
                    $updatedContractEntry = new ContractEntry($updatedContract, $updatedOrderEntry,$counter, 0);
                    $counter++;
                    $this->getEntityManager()->persist($updatedOrderEntry);
                    $this->getEntityManager()->persist($updatedContractEntry);

                }

                $this->getEntityManager()->persist($updatedOrder);
                $this->getEntityManager()->persist($updatedContract);

                $order->setOld();

                $this->getEntityManager()->persist($order);

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
        foreach ($order->getContract()->getEntries() as $contractEntry) {
            $this->getEntityManager()->remove($contractEntry);
            $this->getEntityManager()->flush();
        }

        $this->getEntityManager()->remove($order->getContract());
        $this->getEntityManager()->flush();

        foreach ($order as $orderEntry) {
            $this->getEntityManager()->remove($orderEntry);
            $this->getEntityManager()->flush();
        }
        $this->getEntityManager()->remove($order);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function deleteProductAction()
    {
        $this->initAjax();

        // if (!($order = $this->_getOrder(false)))
        //     return new ViewModel();
        // if($order->getContract()->isSigned() == true)
        //     return new ViewModel();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function historyAction()
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
