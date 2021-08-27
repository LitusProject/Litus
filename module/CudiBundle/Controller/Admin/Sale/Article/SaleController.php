<?php

namespace CudiBundle\Controller\Admin\Sale\Article;

use CudiBundle\Entity\Sale\Article as SaleArticle;
use CudiBundle\Entity\Sale\SaleItem\External as ExternalItem;
use CudiBundle\Entity\Sale\SaleItem\Prof as ProfItem;
use Laminas\View\Model\ViewModel;

/**
 * SaleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SaleController extends \CudiBundle\Component\Controller\ActionController
{
    public function saleAction()
    {
        $article = $this->getSaleArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $period = $this->getActiveStockPeriodEntity();
        if ($period === null) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_sale_article_sale_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                if ($formData['sale_to'] == 'prof') {
                    $saleItem = new ProfItem(
                        $article,
                        $formData['number'],
                        $formData['name'],
                        $this->getEntityManager()
                    );
                } else {
                    $saleItem = new ExternalItem(
                        $article,
                        $formData['number'],
                        $formData['price'],
                        $formData['name'],
                        $this->getEntityManager()
                    );
                }

                $this->getEntityManager()->persist($saleItem);
                $article->setStockValue($article->getStockValue() - $formData['number']);

                $nbToMuchAssigned = $period->getNbAssigned($article) - $article->getStockValue();
                $bookings = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findLastAssignedByArticle($article);

                foreach ($bookings as $booking) {
                    if ($nbToMuchAssigned <= 0) {
                        break;
                    }
                    $booking->setStatus('booked', $this->getEntityManager());
                    $nbToMuchAssigned -= $booking->getNumber();
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The article was successfully sold!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article',
                    array(
                        'action' => 'edit',
                        'id'     => $article->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'article' => $article,
                'form'    => $form,
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
}
