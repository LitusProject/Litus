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

namespace CudiBundle\Controller\Admin\Stock;

use CudiBundle\Entity\Stock\Retour,
    CudiBundle\Form\Admin\Stock\Deliveries\Retour as RetourForm,
    Zend\View\Model\ViewModel;

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
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'suppliers' => $suppliers,
            )
        );
    }

    public function supplierAction()
    {
        if (!($supplier = $this->_getSupplier()))
            return new ViewModel();

        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

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
                'supplier' => $supplier,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
                'suppliers' => $suppliers,
            )
        );
    }

    public function addAction()
    {
        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        $academicYear = $this->getAcademicYear();

        $prefix = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.article_barcode_prefix') . $this->getAcademicYear()->getCode(true);

        $form = new RetourForm($this->getEntityManager(), $prefix);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($formData['article_id']);

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
                'form' => $form,
                'retours' => $retours,
                'suppliers' => $suppliers,
                'currentAcademicYear' => $academicYear,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($retour = $this->_getRetour()))
            return new ViewModel();

        $retour->getArticle()->addStockValue(-$retour->getNumber());
        $this->getEntityManager()->remove($retour);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function _getRetour()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the retour!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_retour',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $retour = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Retour')
            ->findOneById($this->getParam('id'));

        if (null === $retour) {
            $this->flashMessenger()->error(
                'Error',
                'No retour with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_retour',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $retour;
    }

    private function _getSupplier()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the supplier!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_retour',
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
            $this->flashMessenger()->error(
                'Error',
                'No supplier with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_retour',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $supplier;
    }
}
