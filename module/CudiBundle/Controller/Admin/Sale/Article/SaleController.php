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

use CudiBundle\Entity\Sale\Article as SaleArticle;
use CudiBundle\Entity\Sale\SaleItem\External as ExternalItem;
use CudiBundle\Entity\Sale\SaleItem\Prof as ProfItem;
use Zend\View\Model\ViewModel;

/**
 * SaleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SaleController extends \CudiBundle\Component\Controller\ActionController
{
    public function saleAction()
    {
        if (!($article = $this->getSaleArticleEntity())) {
            return new ViewModel();
        }

        if (!($period = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_sale_article_sale_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                if ('prof' == $formData['sale_to']) {
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
