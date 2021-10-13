<?php

namespace CudiBundle\Controller\Admin\Stock;

use CudiBundle\Entity\Stock\Period;
use CudiBundle\Entity\Stock\Period\Value\Start as StartValue;
use Laminas\View\Model\ViewModel;

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
                'paginator'         => $paginator,
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

                $booking->setStatus('expired', $this->getEntityManager());
                $booking->setStatus('booked', $this->getEntityManager());
                $booking->setStatus('assigned', $this->getEntityManager());
            }

            $articles = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period')
                ->findAllArticlesByPeriod($previous);
            foreach ($articles as $article) {
                $value = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Stock\Period\Value\Start')
                    ->findValueByArticleAndPeriod($article, $previous) + $previous->getNbDelivered($article) - $previous->getNbSold($article);

                $start = new StartValue($article, $new, ($value < 0 ? 0 : $value));
                $this->getEntityManager()->persist($start);
            }
        }

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'The stock period was successfully created.'
        );

        $this->redirect()->toRoute(
            'cudi_admin_stock_period',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function revertNewAction()
    {
        $msg = 'The stock period was successfully reverted.';

        /* Find current period */
        $current = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        /* Find previous period */
        $previous = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findLastInactive();

        if ($previous) {
            /* Open the previous period again */
            $previous->open();
        } else {
            $msg .= 'No previous stock period found, none are activated now.';
        }

        /* Remove the current period from existence */
        if ($current) {
            /* Remove newly created start values (associated with the deleted period) */
            $startValues = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period\Value\Start')
                ->findAllByPeriod($current);
            foreach ($startValues as $startValue) {
                $this->getEntityManager()->remove($startValue);
            }

            $this->getEntityManager()->remove($current);
        } else {
            $msg .= 'No current stock period found, none are deleted.';
        }

        /* Flush and perform all database operations */
        $this->getEntityManager()->flush();

        /* Message success */
        $this->flashMessenger()->success(
            'Success',
            $msg
        );

        /* Redirect back to manage */
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
        $period = $this->getPeriodEntity();
        if ($period === null) {
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
                'period'            => $period,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function searchAction()
    {
        $period = $this->getPeriodEntity();
        if ($period === null) {
            return new ViewModel();
        }

        $articles = array();
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
