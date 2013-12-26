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

namespace CudiBundle\Controller\Admin\Sale\Article;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;
/**
 * SaleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SaleController extends \CudiBundle\Component\Controller\ActionController
{
    public function saleAction()
    {
        if (!($saleArticle = $this->_getSaleArticle()))
            return new ViewModel();

        return new ViewModel(
            array(
                'article' => $saleArticle,
            )
        );
    }

    private function _getSaleArticle()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the article!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($this->getParam('id'));

        if (null === $article) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No article with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $article;
    }

    /*public function sellProfAction()
    {
        if (!($saleArticle = $this->_getSaleArticle()))
            return new ViewModel();

        $saleItem = new SaleItem(
            $saleArticle,
            1,
            0,
            null,
            null,
            $this->getEntityManager()
        );
        $this->getEntityManager()->persist($saleItem);

        $this->getEntityManager()->persist(new ProfVersionLog($this->getAuthentication()->getPersonObject(), $saleArticle));

        $saleArticle->setStockValue($saleArticle->getStockValue() - 1);

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The article is successfully sold to a prof'
            )
        );

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);

        return new ViewModel();
    }*/
}