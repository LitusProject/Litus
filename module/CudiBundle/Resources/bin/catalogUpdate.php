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
 * Send mail for catalog updates
 *
 * Usage:
 * --run|-r         Run
 * --mail|-m        Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */

if (false === getenv('APPLICATION_ENV'))
    putenv('APPLICATION_ENV=development');

chdir(dirname(dirname(dirname(dirname(__DIR__)))));

include 'init_autoloader.php';

$application = Zend\Mvc\Application::init(include 'config/application.config.php');
$em = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');
$mt = $application->getServiceManager()->get('mail_transport');

$fallbackLanguage = $em->getRepository('CommonBundle\Entity\General\Language')
    ->findOneByAbbrev(
        $em->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('fallback_language')
    );
\Locale::setDefault($fallbackLanguage->getAbbrev());

$rules = array(
    'run|r-s' => 'Run',
    'mail|m'  => 'Send Mail',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if (isset($opts->r)) {
    $date = new \DateTime();
    $date->sub(new \DateInterval('P1D'));

    $academicYear = getCurrentAcademicYear($em);

    $subjects = array();

    $logs = $em->getRepository('CudiBundle\Entity\Log\Article\Sale\Bookable')
        ->findAllAfter($date);

    foreach ($logs as $log) {
        $article= $log->getArticle($em);

        $mappings = $em->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findAllByArticleAndAcademicYear($article->getMainArticle(), $academicYear);

        foreach ($mappings as $mapping) {
            if (!isset($subjects[$mapping->getSubject()->getId()])) {
                $subjects[$mapping->getSubject()->getId()] = array(
                    'subject' => $mapping->getSubject(),
                    'updates' => array(
                        'bookable' => array(),
                        'unbookable' => array(),
                        'added' => array(),
                        'removed' => array(),
                    )
                );
            }

            $subjects[$mapping->getSubject()->getId()]['updates']['bookable'][] = $article;
        }
    }

    $logs = $em->getRepository('CudiBundle\Entity\Log\Article\Sale\Unbookable')
        ->findAllAfter($date);

    foreach ($logs as $log) {
        $article= $log->getArticle($em);

        $mappings = $em->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findAllByArticleAndAcademicYear($article->getMainArticle(), $academicYear);

        foreach ($mappings as $mapping) {
            if (!isset($subjects[$mapping->getSubject()->getId()])) {
                $subjects[$mapping->getSubject()->getId()] = array(
                    'subject' => $mapping->getSubject(),
                    'updates' => array(
                        'bookable' => array(),
                        'unbookable' => array(),
                        'added' => array(),
                        'removed' => array(),
                    )
                );
            }

            $subjects[$mapping->getSubject()->getId()]['updates']['unbookable'][] = $article;
        }
    }

    $logs = $em->getRepository('CudiBundle\Entity\Log\Article\SubjectMap\Added')
        ->findAllAfter($date);

    foreach ($logs as $log) {
        $subjectMap= $log->getSubjectMap($em);

        if (!isset($subjects[$subjectMap->getSubject()->getId()])) {
            $subjects[$subjectMap->getSubject()->getId()] = array(
                'subject' => $subjectMap->getSubject(),
                'updates' => array(
                    'bookable' => array(),
                    'unbookable' => array(),
                    'added' => array(),
                    'removed' => array(),
                )
            );
        }

        $subjects[$subjectMap->getSubject()->getId()]['updates']['added'][] = $subjectMap->getArticle();
    }

    $logs = $em->getRepository('CudiBundle\Entity\Log\Article\SubjectMap\Removed')
        ->findAllAfter($date);

    foreach ($logs as $log) {
        $subjectMap= $log->getSubjectMap($em);

        if (!isset($subjects[$subjectMap->getSubject()->getId()])) {
            $subjects[$subjectMap->getSubject()->getId()] = array(
                'subject' => $subjectMap->getSubject(),
                'updates' => array(
                    'bookable' => array(),
                    'unbookable' => array(),
                    'added' => array(),
                    'removed' => array(),
                )
            );
        }

        $subjects[$subjectMap->getSubject()->getId()]['updates']['removed'][] = $subjectMap->getArticle();
    }

    $subscribers = $em->getRepository('CudiBundle\Entity\Article\Notification\Subscription')
        ->findAll();

    $mailAddress = $em->getRepository('CommonBundle\Entity\General\Config')
        ->getConfigValue('cudi.mail');

    $mailName = $em->getRepository('CommonBundle\Entity\General\Config')
        ->getConfigValue('cudi.mail_name');

    $counter = 0;

    foreach ($subscribers as $subscription) {
        $academicSubjects = $em->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($subscription->getPerson(), $academicYear);

        if (!($language = $subscription->getPerson()->getLanguage())) {
            $language = $em->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('en');
        }

        $mailData = unserialize(
            $em
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.catalog_update_mail')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $mailSubject = $mailData[$language->getAbbrev()]['subject'];

        preg_match('/#bookable#(.*)#bookable#/', $message, $bookableText);
        $message = preg_replace('/#bookable#.*#bookable#/', '', $message);

        preg_match('/#unbookable#(.*)#unbookable#/', $message, $unbookableText);
        $message = preg_replace('/#unbookable#.*#unbookable#/', '', $message);

        preg_match('/#added#(.*)#added#/', $message, $addedText);
        $message = preg_replace('/#added#.*#added#/', '', $message);

        preg_match('/#removed#(.*)#removed#/', $message, $removedText);
        $message = preg_replace('/#removed#.*#removed#/', '', $message);

        $updates = '';
        foreach ($academicSubjects as $subject) {
            if (!isset($subjects[$subject->getSubject()->getId()]))
                continue;

            $updates .= '* ' . $subject->getSubject()->getName() . ' (' . $subject->getSubject()->getCode() . ')' . "\r\n";

            foreach($subjects[$subject->getSubject()->getId()]['updates']['bookable'] as $log)
                $updates .= '  - ' . $log->getMainArticle()->getTitle() . ' ' . $bookableText[1] . "\r\n";

            foreach($subjects[$subject->getSubject()->getId()]['updates']['unbookable'] as $log)
                $updates .= '  - ' . $log->getMainArticle()->getTitle() . ' ' . $unbookableText[1] . "\r\n";

            foreach($subjects[$subject->getSubject()->getId()]['updates']['added'] as $log)
                $updates .= '  - ' . $log->getTitle() . ' ' . $addedText[1] . "\r\n";

            foreach($subjects[$subject->getSubject()->getId()]['updates']['removed'] as $log)
                $updates .= '  - ' . $log->getTitle() . ' ' . $removedText[1] . "\r\n";
        }

        if ($updates != '') {
            $mail = new \Zend\Mail\Message();
            $mail->setBody(str_replace('{{ updates }}', $updates, $message))
                ->setFrom($mailAddress, $mailName)
                ->addTo($subscription->getPerson()->getEmail(), $subscription->getPerson()->getFullName())
                ->addCc($mailAddress, $mailName)
                ->addBcc(
                    $em->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('system_administrator_mail'),
                    'System Administrator'
                )
                ->setSubject($mailSubject);

            if (isset($opts->m) && 'development' != getenv('APPLICATION_ENV')) {
                $mt->send($mail);
            }
            $counter++;
        }
    }

    if (isset($opts->m))
        echo $counter . ' mails are send';
    else
        echo $counter . ' mails will be send';
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
