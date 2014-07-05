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

namespace CudiBundle\Controller\Sale;

use CudiBundle\Entity\Sale\QueueItem,
    CudiBundle\Entity\Sale\ReturnItem,
    CudiBundle\Form\Sale\Sale\ReturnArticle as ReturnForm,
    Zend\View\Model\ViewModel;

/**
 * SaleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SaleController extends \CudiBundle\Component\Controller\SaleController
{
    public function saleAction()
    {
        $paydesks = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\PayDesk')
            ->findBy(array(), array('name' => 'ASC'));

        $membershipArticles = array();
        $ids = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        foreach ($ids as $organizationId => $articleId) {
            $membershipArticles[$organizationId] = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($articleId);
        }

        return new ViewModel(
            array(
                'socketUrl' => $this->getSocketUrl(),
                'authSession' => $this->getAuthentication()
                    ->getSessionObject(),
                'key' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.queue_socket_key'),
                'paydesks' => $paydesks,
                'membershipArticles' => $membershipArticles,
                'currentAcademicYear' => $this->getCurrentAcademicYear(),
            )
        );
    }

    public function returnAction()
    {
        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOneById($this->getParam('session'));

        $form = new ReturnForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $person = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person')
                    ->findOneById($formData['person_id']);

                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($formData['article_id']);

                $queueItem = new QueueItem($this->getEntityManager(), $person, $session);
                $queueItem->setStatus('sold');
                $this->getEntityManager()->persist($queueItem);

                $saleItem = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                    ->findOneByPersonAndArticle($person, $article);

                if ($saleItem) {
                    $price = $saleItem->getPrice() / $saleItem->getNumber();
                } else {
                    $price = $article->getSellPrice();
                }

                $booking = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findOneSoldByArticleAndPerson($article, $person);

                if ($booking->getNumber() > 1) {
                    $remainder = new Booking(
                        $this->getEntityManager(),
                        $booking->getPerson(),
                        $booking->getArticle(),
                        'returned',
                        1
                    );
                    $this->getEntityManager()->persist($remainder);

                    $booking->setNumber($booking->getNumber() - 1)
                        ->setStatus('sold', $this->getEntityManager());
                } else {
                    $booking->setStatus('returned', $this->getEntityManager());
                }

                $this->getEntityManager()->persist(new ReturnItem($article, $price/100, $queueItem));

                $article->setStockValue($article->getStockValue() + 1);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The sale was successfully returned!'
                );

                $this->redirect()->toRoute(
                    'cudi_sale_sale',
                    array(
                        'action' => 'return',
                        'session' => $session->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'currentAcademicYear' => $this->getCurrentAcademicYear(),
            )
        );
    }

    public function returnPriceAction()
    {
        $this->initAjax();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            if (!isset($data['person']) || !isset($data['article']))
                return new ViewModel();

            $person = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person')
                ->findOneById($data['person']);

            $article = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($data['article']);

            $saleItem = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findOneByPersonAndArticle($person, $article);

            if ($saleItem) {
                $price = $saleItem->getPrice() / $saleItem->getNumber();
            } else {
                $price = $article->getSellPrice();
            }

            return new ViewModel(
                array(
                    'result' => array(
                        'price' => $price,
                    ),
                )
            );
        }

        return new ViewModel();
    }
}
