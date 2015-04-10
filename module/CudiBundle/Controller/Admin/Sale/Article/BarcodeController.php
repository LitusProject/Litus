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

namespace CudiBundle\Controller\Admin\Sale\Article;

use CudiBundle\Entity\Sale\Article as SaleArticle,
    CudiBundle\Entity\Sale\Article\Barcode,
    Zend\View\Model\ViewModel;

/**
 * BarcodeController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BarcodeController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($article = $this->getSaleArticleEntity())) {
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
                        'id' => $article->getId(),
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
                'form' => $form,
                'article' => $article,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($barcode = $this->getBarcodeEntity()) || $barcode->isMain()) {
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
