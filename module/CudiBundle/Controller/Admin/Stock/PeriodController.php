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

use CudiBundle\Entity\Stock\Period,
    CudiBundle\Entity\Stock\Period\Value\Start as StartValue,
    Zend\View\Model\ViewModel;

/**
 * PeriodController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PeriodController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period')
                ->findAllQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function newAction()
    {
        $previous = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        $new = new Period($this->getAuthentication()->getPersonObject());
        $this->getEntityManager()->persist($new);

        if ($previous) {
            $previous->setEntityManager($this->getEntityManager());
            $previous->close();

            $bookings = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findAllBooked($previous);

            foreach ($bookings as $booking) {
                $booking->setStatus('booked', $this->getEntityManager());
            }

            $bookings = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findAllAssigned($previous);

            foreach ($bookings as $booking) {
                if ($booking->getArticle()->canExpire()) {
                    continue;
                }

                $booking->setStatus('booked', $this->getEntityManager());
                $booking->setStatus('assigned', $this->getEntityManager());
            }

            $articles = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period')
                ->findAllArticlesByPeriod($previous);
            foreach ($articles as $article) {
                $value = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Stock\Period\Value\Start')
                        ->findValueByArticleAndPeriod($article, $previous)
                    + $previous->getNbDelivered($article)
                    - $previous->getNbSold($article);

                $start = new StartValue($article, $new, ($value < 0 ? 0 : $value));
                $this->getEntityManager()->persist($start);
            }
        }

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'The stock period was succesfully created.'
        );

        $this->redirect()->toRoute(
            'cudi_admin_stock_period',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function viewAction()
    {
        if (!($period = $this->getPeriodEntity())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period')
                ->findAllArticlesByPeriod($period),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'period' => $period,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function searchAction()
    {
        if (!($period = $this->getPeriodEntity())) {
            return new ViewModel();
        }

        switch ($this->getParam('field')) {
            case 'title':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Period')
                    ->findAllArticlesByPeriodAndTitle($period, $this->getParam('string'));
                break;
            case 'barcode':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Period')
                    ->findAllArticlesByPeriodAndBarcode($period, $this->getParam('string'));
                break;
            case 'supplier':
                $articles = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Period')
                    ->findAllArticlesByPeriodAndSupplier($period, $this->getParam('string'));
                break;
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($articles, $numResults);

        $result = array();
        foreach ($articles as $article) {
            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getMainArticle()->getTitle();
            $item->supplier = $article->getSupplier()->getName();
            $item->delivered = $period->getNbDelivered($article);
            $item->ordered = $period->getNbOrdered($article);
            $item->sold = $period->getNbSold($article);
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return Period|null
     */
    private function getPeriodEntity()
    {
        $period = $this->getEntityById('CudiBundle\Entity\Stock\Period');

        if (!($period instanceof Period)) {
            $this->flashMessenger()->error(
                'Error',
                'No period was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_stock_period',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $period->setEntityManager($this->getEntityManager());

        return $period;
    }
}
