<?php

namespace CudiBundle\Command;

use CudiBundle\Entity\Stock\Period as StockPeriod;

/**
 * Updates catalog
 */
class RecalculateStock extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('cudi:recalculate-stock')
            ->setDescription('Recalculate the stock')
            ->addOption('flush', 'f', null, 'Stores the result in the database');
    }

    protected function invoke()
    {
        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        $period->setEntityManager($this->getEntityManager());

        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findAllArticlesByPeriod($period);

        $membership = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        $this->updateArticles($period, $articles, $membership);

        if ($this->getOption('flush')) {
            $this->write('Flushing entity manager...');
            $this->getEntityManager()->flush();
            $this->writeln(" <fg=green>\u{2713}</fg=green>", true);
        }
    }

    private function updateArticles(StockPeriod $period, array $articles, array $membershipArticles)
    {
        $em = $this->getEntityManager();
        $startRepository = $em->getRepository('CudiBundle\Entity\Stock\Period\Value\Start');
        $deltaRepository = $em->getRepository('CudiBundle\Entity\Stock\Period\Value\Delta');
        $retourRepository = $em->getRepository('CudiBundle\Entity\Stock\Retour');
        $bookingRepository = $em->getRepository('CudiBundle\Entity\Sale\Booking');

        foreach ($articles as $article) {
            if (in_array($article->getId(), $membershipArticles)) {
                $article->setStockValue(0);
                continue;
            }

            $number = $startRepository->findValueByArticleAndPeriod($article, $period) + $period->getNbDelivered($article) - $period->getNbSold($article) + $deltaRepository->findTotalByArticleAndPeriod($article, $period) - $retourRepository->findTotalByArticleAndPeriod($article, $period);

            if ($number < 0) {
                $number = 0;
            }

            if ($article->getStockValue() != $number) {
                $article->setStockValue($number);
                $this->writeln('Updated "' . $article->getMainArticle()->getTitle() . '": <comment>' . $article->getStockValue() . '</comment> to <comment>' . $number . '</comment>');
            }

            $nbToMuchAssigned = $period->getNbAssigned($article) - $article->getStockValue();
            if ($nbToMuchAssigned > 0) {
                $this->writeln('Unassigning "' . $article->getMainArticle()->getTitle() . '" <comment>' . $nbToMuchAssigned . '</comment> times');
                $bookings = $bookingRepository->findLastAssignedByArticle($article);

                foreach ($bookings as $booking) {
                    if ($nbToMuchAssigned <= 0) {
                        break;
                    }
                    $booking->setStatus('booked', $em);
                    $nbToMuchAssigned -= $booking->getNumber();
                }
            }
        }
    }
}
