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

use BrBundle\Entity\Product,
    BrBundle\Form\Admin\Product\Add as AddForm,
    BrBundle\Form\Admin\Product\Edit as EditForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    Zend\View\Model\ViewModel;

/**
 * ProductController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class ProductController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\Product',
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
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $newProduct = new Product(
                    $this->getEntityManager(),
                    $formData['name'],
                    $formData['description'],
                    $formData['invoice_description'],
                    $formData['contract_text'],
                    $this->getAuthentication()->getPersonObject(),
                    $formData['price'],
                    $formData['vat_type'],
                    $this->getCurrentAcademicYear(),
                    DateTime::createFromFormat('d#m#Y', $formData['delivery_date'])
                );

                if ('' != $formData['event']) {
                    $newProduct->setEvent(
                        $this->getEntityManager()
                            ->getRepository('CalendarBundle\Entity\Node\Event')
                            ->findOneById($formData['event'])
                    );
                }

                $this->getEntityManager()->persist($newProduct);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The product was succesfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'br_admin_product',
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
        if (!($product = $this->_getProduct()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $product);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $product->setName($formData['name'])
                    ->setDescription($formData['description'])
                    ->setContractTExt($formData['contract_text'])
                    ->setPrice($formData['price'])
                    ->setVatType($this->getEntityManager(), $formData['vat_type'])
                    ->setInvoiceDescription($formData['invoice_description']);

                if ('' != $formData['event']) {
                    $product->setEvent(
                        $this->getEntityManager()
                            ->getRepository('CalendarBundle\Entity\Node\Event')
                            ->findOneById($formData['event'])
                    );
                } else {
                    $product->setEvent(null);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The product was succesfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'br_admin_product',
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

        if (!($product = $this->_getProduct()))
            return new ViewModel();

        $product->setOld();
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
            'BrBundle\Entity\Product',
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

    private function _getProduct()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the product!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_product',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $product = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Product')
            ->findOneById($this->getParam('id'));

        if (null === $product) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No product with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_product',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $product;
    }
}
