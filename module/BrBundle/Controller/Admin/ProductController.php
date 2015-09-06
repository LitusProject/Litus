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
    CommonBundle\Component\Document\Generator\Csv as CsvGenerator,
    CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    Zend\Http\Headers,
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
        $form = $this->getForm('br_product_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $product = $form->hydrateObject(
                    new Product(
                        $this->getAuthentication()->getPersonObject(),
                        $this->getCurrentAcademicYear()
                    )
                );

                $this->getEntityManager()->persist($product);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The product was succesfully created!'
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
        if (!($product = $this->getProductEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('br_product_edit', array('product' => $product));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The product was succesfully updated!'
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

        if (!($product = $this->getProductEntity())) {
            return new ViewModel();
        }

        $product->setOld();
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function companiesAction()
    {
        if (!($product = $this->getProductEntity())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Product\OrderEntry')
                ->findAllByProductIdQuery($product->getId()),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'em' => $this->getEntityManager(),
                'product' => $product,
            )
        );
    }

    public function companiescsvAction()
    {
        if (!($product = $this->getProductEntity())) {
            return new ViewModel();
        }

        $file = new CsvFile();
        $heading = array('Contract', 'Invoice', 'Company Name', 'Author', 'Contact Person', 'Contact Phone', 'Contact Email', 'Remarks');

        $orderEntries = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Product\OrderEntry')
            ->findAllByProductId($product->getId());

        $results = array();
        foreach ($orderEntries as $entry) {
            if (!$entry->getOrder()->hasContract() || !$entry->getOrder()->getContract()->isSigned()) {
                continue;
            }
            $order = $entry->getOrder();
            $company = $order->getCompany();
            $contract = $order->getContract();

            $contacts = $company->getContacts();
            $contactName = '';
            $contactPhone = $company->getPhoneNumber();
            $contactEmail = '';
            if (count($contacts) > 0) {
                $contact = $contacts[0];
                $contactName = $contact->getFullName();
                $contactPhone = $contact->getPhoneNumber() ? $contact->getPhoneNumber() : $contactPhone;
                $contactEmail = $contact->getEmail() ? $contact->getEmail() : $contactEmail;
            }
            $results[] = array(
                $contract->getFullContractNumber($this->getEntityManager()),
                $order->getInvoice()->getInvoiceNumber($this->getEntityManager()),
                $company->getName(),
                $contract->getAuthor()->getPerson()->getFullName(),
                $contactName,
                $contactPhone,
                $contactEmail,
                '',
            );
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="contracts_overview.csv"',
            'Content-Type'        => 'text/csv',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
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

    /**
     * @return Product|null
     */
    private function getProductEntity()
    {
        $product = $this->getEntityById('BrBundle\Entity\Product');

        if (!($product instanceof Product)) {
            $this->flashMessenger()->error(
                'Error',
                'No company was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_product',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $product;
    }
}
