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

namespace CudiBundle\Controller\Admin\Sale;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * FinancialController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FinancialController extends \CudiBundle\Component\Controller\ActionController
{
    public function salesAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Sale\Session',
            $this->getParam('page'),
            array(),
            array('openDate' => 'DESC')
        );

        foreach($paginator as $item) {
            $item->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function stockAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period')
                ->findAllArticlesByPeriod($period),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'period' => $period,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function suppliersAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Supplier',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'ASC'
            )
        );

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'suppliers' => $suppliers,
            )
        );
    }

    public function deliveriesAction()
    {
        if (!($supplier = $this->_getSupplier()))
            return new ViewModel();

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Stock\Delivery',
            $this->getParam('page'),
            array(),
            array(
                'timestamp' => 'DESC',
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'supplier' => $supplier,
                'suppliers' => $suppliers,
            )
        );
    }

    public function retoursAction()
    {
        if (!($supplier = $this->_getSupplier()))
            return new ViewModel();

        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();

        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\Stock\Retour',
            $this->getParam('page'),
            array(),
            array(
                'timestamp' => 'DESC',
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'supplier' => $supplier,
                'suppliers' => $suppliers,
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
                'cudi_admin_sales_financial',
                array(
                    'action' => 'suppliers'
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
                'cudi_admin_sales_financial',
                array(
                    'action' => 'suppliers'
                )
            );

            return;
        }

        return $supplier;
    }
}
