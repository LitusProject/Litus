<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Product\Order,
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

                $company = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findOneById($formData['company']);

                $order = new Order(
                    $company,
                    $this->getAuthentication()->getPersonObject()
                );

                $this->getEntityManager()->persist($order);

                $products = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Product')
                    ->findByAcademicYear($this->getCurrentAcademicYear());

                foreach ($products as $product)
                {
                    $quantity = $formData['product-' . $product->getId()];
                    if ($quantity != 0)
                    {
                        $orderEntry = new OrderEntry($order, $product, $quantity);
                        $this->getEntityManager()->persist($orderEntry);
                    }
                }

                $this->getEntityManager()->flush();

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
        if (!($order = $this->_getOrder()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $this->getCurrentAcademicYear(), $order);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

                $company = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findOneById($formData['company']);

                $order->setCompany($company);

                // Remove old entries
                foreach ($order->getEntries() as $entry)
                {
                    $this->getEntityManager()->remove($entry);
                }

                $products = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Product')
                    ->findByAcademicYear($this->getCurrentAcademicYear());

                foreach ($products as $product)
                {
                    $quantity = $formData['product-' . $product->getId()];
                    if ($quantity != 0)
                    {
                        $orderEntry = new OrderEntry($order, $product, $quantity);
                        $this->getEntityManager()->persist($orderEntry);
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

    private function _getOrder()
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

        return $order;
    }
}
