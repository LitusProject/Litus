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
    'article|a-s' => 'Article',
    'flush|f'     => 'Flush',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if (isset($opts->a)) {
    $article = $em->getRepository('CudiBundle\Entity\Sales\Article')
        ->findOneById($opts->a);

    $academicYear = getCurrentAcademicYear($em);

    echo 'Article to be assigned: ' . $article->getMainArticle()->getTitle() . PHP_EOL;

    $people = $em->getRepository('SecretaryBundle\Entity\Organization\MetaData')
        ->findBy(array('irreeelAtCudi' => 'true'));

    $number = 0;
    foreach($people as $person) {
        $registration = $em->getRepository('SecretaryBundle\Entity\Registration')
            ->findOneByAcademic($person->getAcademic());
        if (null === $registration)
            continue;
        
        if ($person->getAcademic()->isMember($academicYear) && $registration->hasPayed()) {
            $number++;
            $booking = new \CudiBundle\Entity\Sales\Booking($em, $person->getAcademic(), $article, 'assigned', 1, true);
            $em->persist($booking);

            if (isset($opts->f))
                \CudiBundle\Component\Mail\Booking::sendMail($em, $mt, array($booking), $booking->getPerson());
        }
    }

    echo 'Number that will be assigned: ' . $number . PHP_EOL;
    echo 'Number in stock: ' . $article->getStockValue() . PHP_EOL;

    if (isset($opts->f)) {
        $em->flush();
        echo $article->getMainArticle()->getTitle() . ' assigned' . PHP_EOL;
    }
}

function getCurrentAcademicYear($em)
{
    $startAcademicYear = \CommonBundle\Component\Util\AcademicYear::getStartOfAcademicYear();
    $startAcademicYear->setTime(0, 0);

    $academicYear = $em
        ->getRepository('CommonBundle\Entity\General\AcademicYear')
        ->findOneByUniversityStart($startAcademicYear);

    return $academicYear;
}
