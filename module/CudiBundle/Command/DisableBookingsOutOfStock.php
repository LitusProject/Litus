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

namespace CudiBundle\Command;

/**
 * Disable bookings
 */
class DisableBookingsOutOfStock extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this
            ->setName('cudi:disable-bookings-out-of-stock')
            ->setDescription('Disable bookings for articles which aren\'t in stock')
            ->addOption('flush', 'f', null, 'Stores the result in the database')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command disables bookings for articles which has a stock value of 0.
EOT
            );
    }

    protected function executeCommand()
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
            $this->writeln(' done.', true);
        }
    }

    protected function getLogName()
    {
        return 'DisableBookingsOutOfStock';
    }
}
