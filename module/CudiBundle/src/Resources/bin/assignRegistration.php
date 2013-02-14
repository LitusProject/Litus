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
 * Assign an Ir.Reëel to all users who receive one at CuDi.
 *
 * Usage:
 * --article|-r     Article
 * --flush|-f       Flush
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */

chdir(dirname(dirname(dirname(dirname(dirname(__DIR__))))));

include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$em = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');
$mt = $application->getServiceManager()->get('mail_transport');

$rules = array(
    'run|r'   => 'Run',
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
    $ids = array_merge(
        array_values(
            unserialize(
                $em->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.tshirt_article')
            )
        ),
        unserialize(
            $em->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.registration_articles')
        ),
        array(
            (int) $em->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article'),
            426,
            562,
        )
    );

    $period = $em
        ->getRepository('CudiBundle\Entity\Stock\Period')
        ->findOneActive();

    foreach($ids as $id) {
        $article = $em->getRepository('CudiBundle\Entity\Sales\Article')
            ->findOneById($id);

        echo 'Article to be assigned: ' . $article->getMainArticle()->getTitle() . PHP_EOL;

        $bookings = $em->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findAllBookedByArticleAndPeriod($article, $period);

        foreach($bookings as $booking) {
            $booking->setStatus('assigned', $em);
        }

        echo 'Assigned it ' . sizeof($bookings) . ' times' . PHP_EOL;
    }

    if (isset($opts->f)) {
        $em->flush();
    }
}