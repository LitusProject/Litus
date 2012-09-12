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

namespace CudiBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\General\Address,
    CudiBundle\Entity\Supplier,
    CudiBundle\Form\Admin\Supplier\Add as AddForm,
    CudiBundle\Form\Admin\Supplier\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * SupplierController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SupplierController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Supplier',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'ASC'
            )
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
        $form = new AddForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $supplier = new Supplier(
                    $formData['name'],
                    $formData['phone_number'],
                    new Address(
                        $formData['address_street'],
                        $formData['address_number'],
                        $formData['address_postal'],
                        $formData['address_city'],
                        $formData['address_country']
                    ),
                    $formData['vat_number']
                );
                $this->getEntityManager()->persist($supplier);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The supplier was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_supplier',
                    array(
                        'action' => 'manage'
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
        if (!($supplier = $this->_getSupplier()))
            return new ViewModel();

        $form = new EditForm($supplier);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $supplier->setName($formData['name'])
                    ->setPhoneNumber($formData['phone_number'])
                    ->setVatNumber($formData['vat_number'])
                    ->getAddress()
                        ->setStreet($formData['address_street'])
                        ->setNumber($formData['address_number'])
                        ->setPostal($formData['address_postal'])
                        ->setCity($formData['address_city'])
                        ->setCountry($formData['address_country']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The supplier was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_supplier',
                    array(
                        'action' => 'manage'
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

    private function _getSupplier()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the supplier!'
                )
            );

            $this->redirect()->toRoute(
                'admin_supplier',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $supplier = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findOneById($this->getParam('id'));

        if (null === $supplier) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No supplier with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_supplier',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $supplier;
    }
}
