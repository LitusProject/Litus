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

use BrBundle\Entity\Collaborator,
    BrBundle\Entity\Product\Order,
    BrBundle\Entity\Product\OrderEntry,
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
            $this->getParam('page'),
            array(
                'old' => false,
            )
        );

        foreach ($paginator as $order) {
            $order->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        if (!($collaborator = $this->getCollaboratorEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('br_order_add', array('current_year' => $this->getCurrentAcademicYear()));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $order = $form->hydrateObject(
                    new Order($collaborator)
                );

                $this->getEntityManager()->persist($order);
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
        if (!($order = $this->getOrderEntity(false))) {
            return new ViewModel();
        }

        if ($order->getContract()->isSigned()) {
            return new ViewModel();
        }

        if (!($collaborator = $this->getCollaboratorEntity())) {
            return new ViewModel();
        }

        $entries = $order->getEntries();

        $currentProducts = array();
        foreach ($entries as $entry) {
            $currentProducts[] = $entry->getProduct();
        }

        $form = $this->getForm('br_order_add-product', array('order' => $order, 'current_products' => $currentProducts, 'current_year' => $this->getCurrentAcademicYear()));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'br_admin_order',
                    array(
                        'action' => 'manage',
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'order' => $order,
                'entries' => $entries,
                'addProductForm' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($order = $this->getOrderEntity(false))) {
            return new ViewModel();
        }

        if ($order->getContract()->isSigned()) {
            return new ViewModel();
        }

        if (!($collaborator = $this->getCollaboratorEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('br_order_edit', array('order' => $order, 'current_year' => $this->getCurrentAcademicYear()));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The order was succesfully updated!'
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

        if (!($order = $this->getOrderEntity())) {
            return new ViewModel();
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

        if (!($entry = $this->getEntryEntity(false))) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($entry);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\Product\Order',
            $this->getParam('page'),
            array(
                'old' => true,
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    /**
     * @param  boolean    $allowSigned
     * @return Order|null
     */
    private function getOrderEntity($allowSigned = true)
    {
        $order = $this->getEntityById('BrBundle\Entity\Product\Order');

        if (!($order instanceof Order)) {
            $this->flashMessenger()->error(
                'Error',
                'No order was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        if ($order->getContract()->isSigned() && !$allowSigned) {
            $this->flashMessenger()->error(
                'Error',
                'The given order\'s contract has been signed! Signed orders cannot be modified.'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $order;
    }

    /**
     * @param  boolean         $allowSigned
     * @return OrderEntry|null
     */
    private function getEntryEntity($allowSigned = true)
    {
        $entry = $this->getEntityById('BrBundle\Entity\Product\OrderEntry');

        if (!($entry instanceof OrderEntry)) {
            $this->flashMessenger()->error(
                'Error',
                'No entry was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        if ($entry->getOrder()->getContract()->isSigned() && !$allowSigned) {
            $this->flashMessenger()->error(
                'Error',
                'The given order\'s contract has been signed! Signed orders cannot be modified.'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $entry;
    }

    /**
     * @return Collaborator|null
     */
    private function getCollaboratorEntity()
    {
        $collaborator = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Collaborator')
            ->findCollaboratorByPersonId($this->getAuthentication()->getPersonObject()->getId());

        if (null === $collaborator) {
            $this->flashMessenger()->error(
                'Error',
                'You are not a collaborator, so you cannot add or edit orders.'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $collaborator;
    }
}
