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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Admin\Sale\Article;

use CudiBundle\Entity\Sale\Article as SaleArticle,
    CudiBundle\Entity\Sale\Article\Discount\Discount,
    Zend\View\Model\ViewModel;

/**
 * DiscountController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class DiscountController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($article = $this->getSaleArticleEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_sale_article_discount_add', array('article' => $article));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $discount = new Discount($article);

                if ($formData['organization'] != '0') {
                    $organization = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Organization')
                        ->findOneById($formData['organization']);
                } else {
                    $organization = null;
                }

                if ($formData['template'] == 0) {
                    $discount->setDiscount(
                        $formData['value'],
                        $formData['method'],
                        $formData['type'],
                        $formData['rounding'],
                        $formData['apply_once'],
                        $organization
                    );
                } else {
                    $template = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Article\Discount\Template')
                        ->findOneById($formData['template']);

                    $discount->setTemplate($template);
                }

                $this->getEntityManager()->persist($discount);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The discount was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article_discount',
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
                ->getRepository('CudiBundle\Entity\Sale\Article\Discount\Discount')
                ->findAllByArticleQuery($article),
            $this->getParam('page')
        );

        $templates = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article\Discount\Template')
            ->findAll();

        return new ViewModel(
            array(
                'article' => $article,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'form' => $form,
                'templates' => $templates,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($discount = $this->getDiscountEntity())) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($discount);
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
     * @return Discount|null
     */
    private function getDiscountEntity()
    {
        $discount = $this->getEntityById('CudiBundle\Entity\Sale\Article\Discount\Discount');

        if (!($discount instanceof Discount)) {
            $this->flashMessenger()->error(
                'Error',
                'No discount was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $discount;
    }
}
