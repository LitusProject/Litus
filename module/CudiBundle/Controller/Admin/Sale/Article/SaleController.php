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

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Sale\SaleItem\Prof as ProfItem,
    CudiBundle\Entity\Sale\SaleItem\External as ExternalItem,
    CudiBundle\Form\Admin\Sales\Article\Sales\Add as AddForm,
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
        if (!($article = $this->_getSaleArticle()))
            return new ViewModel();

        if (!($period = $this->getActiveStockPeriod()))
            return new ViewModel();

        $form = new AddForm();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $saleItem = new \CudiBundle\Entity\Sale\SaleItem(
                        $article,
                        $formData['number'],
                        0,
                        null,
                        null,
                        $this->getEntityManager()
                    );

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

                foreach($bookings as $booking) {
                    if ($nbToMuchAssigned <= 0)
                        break;
                    $booking->setStatus('booked', $this->getEntityManager());
                    $nbToMuchAssigned -= $booking->getNumber();
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The article was successfully sold!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_article',
                    array(
                        'action' => 'edit',
                        'id' => $article->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'article' => $article,
                'form' => $form,
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
}