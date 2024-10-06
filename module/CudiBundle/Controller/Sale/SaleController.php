<?php

namespace CudiBundle\Controller\Sale;

use CudiBundle\Entity\Sale\Booking;
use CudiBundle\Entity\Sale\QueueItem;
use CudiBundle\Entity\Sale\ReturnItem;
use Laminas\View\Model\ViewModel;

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
                'socketUrl'            => $this->getSocketUrl(),
                'authSession'          => $this->getAuthentication()
                    ->getSessionObject(),
                'key'                  => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.queue_socket_key'),
                'paydesks'             => $paydesks,
                'membershipArticles'   => $membershipArticles,
                'currentAcademicYear'  => $this->getCurrentAcademicYear(),
                'printCollectAsSignin' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.print_collect_as_signin'),
            )
        );
    }

    public function returnAction()
    {
        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOneById($this->getParam('session'));

        $form = $this->getForm('cudi_sale_sale_return-article');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $person = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person')
                    ->findOneById($formData['person']['id']);

                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($formData['article']['id']);

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
                    ->findOneSoldByArticleAndPerson($article, $person, false);

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

                $this->getEntityManager()->persist(new ReturnItem($article, $price / 100, $queueItem));

                $article->setStockValue($article->getStockValue() + 1);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The sale was successfully returned!'
                );

                $this->redirect()->toRoute(
                    'cudi_sale_sale',
                    array(
                        'action'  => 'return',
                        'session' => $session->getId(),
                    )
                );

                return new ViewModel(
                    array(
                        'currentAcademicYear' => $this->getCurrentAcademicYear(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form'                => $form,
                'currentAcademicYear' => $this->getCurrentAcademicYear(),
            )
        );
    }

    public function getBoughtItemsAction()
    {
        $personId = $this->params()->fromRoute('session', null);
        error_log('PersonId: ' . $personId);
        error_log('Request URI: ' . $_SERVER['REQUEST_URI']);
        error_log('Referrer: ' . $_SERVER['HTTP_REFERER']);

        if (!$personId) {
            return $this->getResponse()->setStatusCode(400); // Bad Request
        }

        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select('r.id')
            ->from('CudiBundle\Entity\Sale\Booking', 'r')
            ->setMaxResults(10000);

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('b', 'a.title AS articleName')
            ->from('CudiBundle\Entity\Sale\Booking', 'b')
            ->innerJoin('CudiBundle\Entity\Sale\Article', 'sa', 'WITH', 'b.article = sa.id')
            ->innerJoin('CudiBundle\Entity\Article', 'a', 'WITH', 'sa.mainArticle = a.id')
            ->where($queryBuilder->expr()->in('b.id', $subQuery->getDQL()))
            ->andWhere('b.person = :personId')
            ->andWhere('b.status = :status')
            ->setParameter('personId', $personId)
            ->setParameter('status', 'sold');


        $query = $queryBuilder->getQuery();
        $items = $query->getResult();


        $serializedItems = array_map(
            function ($item) {
                return array(
                    'title'       => $item['articleName'],
                    'articleId'   => $item[0]->getArticle()->getId(),
                    'saleDate'    => $item[0]->getSaleDate()->format('Y-m-d H:i:s'),
                    // Add other necessary properties here
                );
            },
            $items
        );
        error_log('Items length: ' . count($serializedItems));

        return new ViewModel(
            array(
                'result' => array(
                    'items' => $serializedItems,
                ),
            )
        );
    }

    public function returnPriceAction()
    {
        $this->initAjax();

        $form = $this->getForm('cudi_sale_sale_return-article');

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $form->setData($data);

            if ($form->isValid()) {
                $person = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person')
                    ->findOneById($data['person']['id']);

                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($data['article']['id']);

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
        }

        return new ViewModel(
            array(
                'result' => array(
                    'error' => 'form_error',
                ),
            )
        );
    }
}
