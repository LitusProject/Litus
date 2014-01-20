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

/**
 * Update the stock.
 *
 * Usage:
 * --run|-r      Recalculate Stock
 * --flush|-f    Flush the results
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */

if (false === getenv('APPLICATION_ENV'))
    putenv('APPLICATION_ENV=development');

chdir(dirname(dirname(dirname(dirname(__DIR__)))));

include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$em = $application->getServiceManager()->get('doctrine.em.orm_default');

$fallbackLanguage = $em->getRepository('CommonBundle\Entity\General\Language')
    ->findOneByAbbrev(
        $em->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('fallback_language')
    );
\Locale::setDefault($fallbackLanguage->getAbbrev());

$rules = array(
    'run|r'   => 'Recalculate Stock',
    'flush|f' => 'Flush',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if (isset($opts->r)) {
    echo 'Updating stock...' . PHP_EOL;

    $period = $em->getRepository('CudiBundle\Entity\Stock\Period')
        ->findOneActive();

    $period->setem($em);

    $articles = $em->getRepository('CudiBundle\Entity\Stock\Period')
        ->findAllArticlesByPeriod($period);

    $membership = unserialize(
        $this->getem()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.membership_article')
    );

    foreach($articles as $article) {
        if (is_array($article->getId(), $membership)) {
            $article->setStockValue(0);
            continue;
        }

        $number = $em->getRepository('CudiBundle\Entity\Stock\Period\Value\Start')
                ->findValueByArticleAndPeriod($article, $period)
            + $period->getNbDelivered($article) - $period->getNbSold($article)
            + $em->getRepository('CudiBundle\Entity\Stock\Period\Value\Delta')
                ->findTotalByArticleAndPeriod($article, $period)
            - $em->getRepository('CudiBundle\Entity\Stock\Retour')
                ->findTotalByArticleAndPeriod($article, $period);

        if ($number < 0)
            $number = 0;

        if ($article->getStockValue() != $number) {
            echo 'Updated ' . $article->getMainArticle()->getTitle() . ': ' . $article->getStockValue() . ' to ' . $number . PHP_EOL;
            $article->setStockValue($number);
        }

        $nbToMuchAssigned = $period->getNbAssigned($article) - $article->getStockValue();
        if ($nbToMuchAssigned > 0) {
            echo 'Unassign ' . $article->getMainArticle()->getTitle() . ' (' . $nbToMuchAssigned . ' times)' . PHP_EOL;
            $bookings = $em->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findLastAssignedByArticle($article);

            foreach($bookings as $booking) {
                if ($nbToMuchAssigned <= 0)
                    break;
                $booking->setStatus('booked', $em);
                $nbToMuchAssigned -= $booking->getNumber();
            }
        }
    }

    if (isset($opts->f)) {
        $em->flush();
    }
}
