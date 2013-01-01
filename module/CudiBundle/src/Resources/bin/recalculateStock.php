<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

/**
 * Update the stock.
 *
 * Usage:
 * --run|-r      Recalculate Stock
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */

chdir(dirname(dirname(dirname(dirname(dirname(__DIR__))))));

include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$entityManager = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');

$rules = array(
    'run|r' => 'Recalculate Stock',
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

    $period = $entityManager
        ->getRepository('CudiBundle\Entity\Stock\Period')
        ->findOneActive();

    $period->setEntityManager($entityManager);

    $articles = $entityManager
        ->getRepository('CudiBundle\Entity\Stock\Period')
        ->findAllArticlesByPeriod($period);

    foreach($articles as $article) {
        $number = $entityManager
                ->getRepository('CudiBundle\Entity\Stock\Periods\Values\Start')
                ->findValueByArticleAndPeriod($article, $period)
            + $period->getNbDelivered($article) - $period->getNbSold($article);

        if ($number < 0)
            $number = 0;

        if ($article->getStockValue() != $number) {
            echo 'Updated ' . $article->getMainArticle()->getTitle() . ': ' . $article->getStockValue() . ' to ' . $number . PHP_EOL;
            $article->setStockValue($number);
        }
    }

    $entityManager->flush();
}
