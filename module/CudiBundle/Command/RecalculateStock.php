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

namespace CudiBundle\Command;

use CudiBundle\Entity\Stock\Period as StockPeriod,
    CudiBundle\Entity\Sale\Article;

/**
 * Updates catalog
 */
class RecalculateStock extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this
            ->setName('cudi:stock:recalculate')
            ->setAliases(array('cudi:recalculate-stock'))
            ->setDescription('Recalculate the stock.')
            ->addOption('flush', 'f', null, 'Stores the result in the database.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command recalculates the stock and stores it if <comment>--flush</comment> is given.
EOT
        );
    }

    protected function executeCommand()
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

        $this->_updateArticles($period, $articles, $membership);

        if ($this->getOption('flush')) {
            $this->write('Flushing entity manager...');
            $this->getEntityManager()->flush();
            $this->writeln(' done.', true);
        }
    }

    protected function getLogName()
    {
        return 'RecalculateStock';
    }

    private function _updateArticles(StockPeriod $period, array $articles, array $membershipArticles)
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

            $number = $startRepository->findValueByArticleAndPeriod($article, $period)
                + $period->getNbDelivered($article) - $period->getNbSold($article)
                + $deltaRepository->findTotalByArticleAndPeriod($article, $period)
                - $retourRepository->findTotalByArticleAndPeriod($article, $period);

            if ($number < 0)
                $number = 0;

            if ($article->getStockValue() != $number) {
                $this->writeln('Updated "' . $article->getMainArticle()->getTitle() . '": <comment>'
                    . $article->getStockValue() . '</comment> to <comment>' . $number . '</comment>');
                $article->setStockValue($number);
            }

            $nbToMuchAssigned = $period->getNbAssigned($article) - $article->getStockValue();
            if ($nbToMuchAssigned > 0) {
                $this->writeln('Unassigning "' . $article->getMainArticle()->getTitle() . '" <comment>' . $nbToMuchAssigned . '</comment> times');
                $bookings = $bookingRepository->findLastAssignedByArticle($article);

                foreach ($bookings as $booking) {
                    if ($nbToMuchAssigned <= 0)
                        break;
                    $booking->setStatus('booked', $em);
                    $nbToMuchAssigned -= $booking->getNumber();
                }
            }
        }
    }
}
