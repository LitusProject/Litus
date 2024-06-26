<?php

namespace CudiBundle\Controller\Admin\Sale\Article;

use CudiBundle\Entity\Sale\Article as SaleArticle;
use CudiBundle\Entity\Sale\Article\Barcode;
use Laminas\View\Model\ViewModel;

/**
 * BarcodeController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BarcodeController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $article = $this->getSaleArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_sale_article_barcode_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $article->addBarcode(new Barcode($article, $formData['barcode']));

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The barcode was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article_barcode',
                    array(
                        'action' => 'manage',
                        'id'     => $article->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article\Barcode')
                ->findAllByArticleQuery($article),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'form'              => $form,
                'article'           => $article,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $barcode = $this->getBarcodeEntity();
        if ($barcode === null) {
            return new ViewModel();
        }

        if ($barcode->isMain()) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($barcode);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return SaleArticle|null
     */
    private function getSaleArticleEntity()
    {
        $article = $this->getEntityById('CudiBundle\Entity\Sale\Article');

        if (!($article instanceof SaleArticle)) {
            $this->flashMessenger()->error(
                'Error',
                'No article was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $article;
    }

    /**
     * @return Barcode|null
     */
    private function getBarcodeEntity()
    {
        $barcode = $this->getEntityById('CudiBundle\Entity\Sale\Article\Barcode');

        if (!($barcode instanceof Barcode)) {
            $this->flashMessenger()->error(
                'Error',
                'No barcode was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $barcode;
    }
}
