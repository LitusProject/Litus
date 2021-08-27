<?php

namespace CudiBundle\Command;

/**
 * Disable bookings
 */
class DisableBookingsOutOfStock extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('cudi:disable-bookings-out-of-stock')
            ->setDescription('Disable bookings for articles which aren\'t in stock')
            ->addOption('flush', 'f', null, 'Stores the result in the database');
    }

    protected function invoke()
    {
        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByAcademicYear($this->getCurrentAcademicYear());

        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        $period->setEntityManager($this->getEntityManager());

        foreach ($articles as $article) {
            if ($article->getStockValue() - $period->getNbAssigned($article) - $period->getNbBooked($article) <= 0 && $article->isBookable()) {
                $this->writeln('Disable booking for <comment>' . $article->getMainArticle()->getTitle() . '</comment>');
                $article->setIsBookable(false);
            }
        }

        if ($this->getOption('flush')) {
            $this->write('Flushing entity manager...');
            $this->getEntityManager()->flush();
            $this->writeln(" <fg=green>\u{2713}</fg=green>", true);
        }
    }
}
