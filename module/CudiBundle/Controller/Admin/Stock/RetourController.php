<?php

namespace CudiBundle\Controller\Admin\Stock;

use CudiBundle\Entity\Stock\Retour;
use CudiBundle\Entity\Supplier;
use Laminas\View\Model\ViewModel;

/**
 * RetourController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RetourController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Supplier')
                ->findAllQuery(),
            $this->getParam('page')
        );

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'suppliers'         => $suppliers,
            )
        );
    }

    public function supplierAction()
    {
        $supplier = $this->getSupplierEntity();
        if ($supplier === null) {
            return new ViewModel();
        }

        $period = $this->getActiveStockPeriodEntity();
        if ($period === null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Retour')
                ->findAllBySupplierAndPeriodQuery($supplier, $period),
            $this->getParam('page')
        );

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'supplier'          => $supplier,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
                'suppliers'         => $suppliers,
            )
        );
    }

    public function addAction()
    {
        $period = $this->getActiveStockPeriodEntity();
        if ($period === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();

        $prefix = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.article_barcode_prefix') . $this->getAcademicYearEntity()->getCode(true);

        $form = $this->getForm(
            'cudi_stock_delivery_retour',
            array(
                'barcode_prefix' => $prefix,
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($formData['article']['id']);

                $item = new Retour($article, $formData['number'], $this->getAuthentication()->getPersonObject(), $formData['comment']);
                $this->getEntityManager()->persist($item);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The retour was successfully added!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_stock_retour',
                    array(
                        'action' => 'add',
                    )
                );

                return new ViewModel(
                    array(
                        'currentAcademicYear' => $academicYear,
                    )
                );
            }
        }

        $retours = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Retour')
            ->findAllByPeriodQuery($period)
            ->setMaxResults(25)
            ->getResult();

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'form'                => $form,
                'retours'             => $retours,
                'suppliers'           => $suppliers,
                'currentAcademicYear' => $academicYear,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $retour = $this->getRetourEntity();
        if ($retour === null) {
            return new ViewModel();
        }

        $retour->getArticle()->addStockValue(-$retour->getNumber());
        $this->getEntityManager()->remove($retour);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Retour|null
     */
    private function getRetourEntity()
    {
        $retour = $this->getEntityById('CudiBundle\Entity\Stock\Retour');

        if (!($retour instanceof Retour)) {
            $this->flashMessenger()->error(
                'Error',
                'No retour was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_retour',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $retour;
    }

    /**
     * @return Supplier|null
     */
    private function getSupplierEntity()
    {
        $supplier = $this->getEntityById('CudiBundle\Entity\Supplier');

        if (!($supplier instanceof Supplier)) {
            $this->flashMessenger()->error(
                'Error',
                'No supplier was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_retour',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $supplier;
    }
}
