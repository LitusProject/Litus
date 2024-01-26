<?php

namespace CudiBundle\Command;

use CommonBundle\Entity\General\AcademicYear;
use DateInterval;
use DateTime;
use Laminas\Mail\Message as Mail;

/**
 * CatalogUpdate
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CatalogUpdate extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('cudi:update-catalog')
            ->setDescription('Update the catalog')
            ->addOption('mail', 'm', null, 'Send mails to users to notify them of the update');
    }

    protected function invoke()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));

        $academicYear = $this->getCurrentAcademicYear(true);
        $subjects = array();

        $this->findAllBookable($subjects, $date, $academicYear);
        $this->findAllUnbookable($subjects, $date, $academicYear);
        $this->findAllAdded($subjects, $date);
        $this->findAllRemoved($subjects, $date);

        $this->writeln('A total of <comment>' . count($subjects) . '</comment> subjects is affected');

        $this->notifySubscribers($subjects, $academicYear);
    }

    private function findAllBookable(array &$subjects, DateTime $date, AcademicYear $academicYear)
    {
        $logs = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Log\Article\Sale\Bookable')
            ->findAllAfter($date);

        $this->writeln('Found <comment>' . count($logs) . '</comment> log entries for <info>Bookable</info>');

        foreach ($logs as $log) {
            $article = $log->getArticle($this->getEntityManager());

            $mappings = $this->getEntityManager()->getRepository('CudiBundle\Entity\Article\SubjectMap')
                ->findAllByArticleAndAcademicYear($article->getMainArticle(), $academicYear);

            foreach ($mappings as $mapping) {
                if (!isset($subjects[$mapping->getSubject()->getId()])) {
                    $subjects[$mapping->getSubject()->getId()] = array(
                        'subject' => $mapping->getSubject(),
                        'updates' => array(
                            'bookable'   => array(),
                            'unbookable' => array(),
                            'added'      => array(),
                            'removed'    => array(),
                        ),
                    );
                }

                $subjects[$mapping->getSubject()->getId()]['updates']['bookable'][] = $article;
            }
        }
    }

    private function findAllUnbookable(array &$subjects, DateTime $date, AcademicYear $academicYear)
    {
        $logs = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Log\Article\Sale\Unbookable')
            ->findAllAfter($date);
        $this->writeln('Found <comment>' . count($logs) . '</comment> log entries for <info>Unbookable</info>');

        foreach ($logs as $log) {
            $article = $log->getArticle($this->getEntityManager());

            $mappings = $this->getEntityManager()->getRepository('CudiBundle\Entity\Article\SubjectMap')
                ->findAllByArticleAndAcademicYear($article->getMainArticle(), $academicYear);

            foreach ($mappings as $mapping) {
                if (!isset($subjects[$mapping->getSubject()->getId()])) {
                    $subjects[$mapping->getSubject()->getId()] = array(
                        'subject' => $mapping->getSubject(),
                        'updates' => array(
                            'bookable'   => array(),
                            'unbookable' => array(),
                            'added'      => array(),
                            'removed'    => array(),
                        ),
                    );
                }

                $subjects[$mapping->getSubject()->getId()]['updates']['unbookable'][] = $article;
            }
        }
    }

    private function findAllAdded(array &$subjects, DateTime $date)
    {
        $logs = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Log\Article\SubjectMap\Added')
            ->findAllAfter($date);

        $this->writeln('Found <comment>' . count($logs) . '</comment> log entries for <info>Added</info>');

        foreach ($logs as $log) {
            $subjectMap = $log->getSubjectMap($this->getEntityManager());

            if (!isset($subjects[$subjectMap->getSubject()->getId()])) {
                $subjects[$subjectMap->getSubject()->getId()] = array(
                    'subject' => $subjectMap->getSubject(),
                    'updates' => array(
                        'bookable'   => array(),
                        'unbookable' => array(),
                        'added'      => array(),
                        'removed'    => array(),
                    ),
                );
            }

            $subjects[$subjectMap->getSubject()->getId()]['updates']['added'][] = $subjectMap->getArticle();
        }
    }

    private function findAllRemoved(array &$subjects, DateTime $date)
    {
        $logs = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Log\Article\SubjectMap\Removed')
            ->findAllAfter($date);
        $this->writeln('Found <comment>' . count($logs) . '</comment> log entries for <info>Removed</info>');

        foreach ($logs as $log) {
            $subjectMap = $log->getSubjectMap($this->getEntityManager());

            if (!isset($subjects[$subjectMap->getSubject()->getId()])) {
                $subjects[$subjectMap->getSubject()->getId()] = array(
                    'subject' => $subjectMap->getSubject(),
                    'updates' => array(
                        'bookable'   => array(),
                        'unbookable' => array(),
                        'added'      => array(),
                        'removed'    => array(),
                    ),
                );
            }

            $subjects[$subjectMap->getSubject()->getId()]['updates']['removed'][] = $subjectMap->getArticle();
        }
    }

    private function notifySubscribers(array $subjects, AcademicYear $academicYear)
    {
        $subscribers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\Notification\Subscription')
            ->findAll();

        $mailAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.mail');

        $mailName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.mail_name');

        $counter = 0;

        $sendMails = $this->getOption('mail');
        $mailEnabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.catalog_update_mail_enabled') === '1';

        if ($sendMails && !$mailEnabled) {
            $sendMails = false;
            $this->writeln('<error>The mails will not be sent because they are disabled!</error>');
        }

        if ($sendMails && getenv('APPLICATION_ENV') == 'development') {
            $sendMails = false;
            $this->writeln('<error>The mails will not be sent because the application is running in development mode!</error>');
        }

        foreach ($subscribers as $subscription) {
            $academicSubjects = $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Subject')
                ->findAllByAcademicAndAcademicYear($subscription->getPerson(), $academicYear);

            $language = $subscription->getPerson()->getLanguage();
            if ($language === null) {
                $language = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findOneByAbbrev('en');
            }

            $mailData = unserialize(
                $this->getEntityManager()
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
                if (!isset($subjects[$subject->getSubject()->getId()])) {
                    continue;
                }

                $updates .= '* ' . $subject->getSubject()->getName() . ' (' . $subject->getSubject()->getCode() . ')' . "\r\n";

                foreach ($subjects[$subject->getSubject()->getId()]['updates']['bookable'] as $log) {
                    $updates .= '  - ' . $log->getMainArticle()->getTitle() . ' ' . $bookableText[1] . "\r\n";
                }

                foreach ($subjects[$subject->getSubject()->getId()]['updates']['unbookable'] as $log) {
                    $updates .= '  - ' . $log->getMainArticle()->getTitle() . ' ' . $unbookableText[1] . "\r\n";
                }

                foreach ($subjects[$subject->getSubject()->getId()]['updates']['added'] as $log) {
                    $updates .= '  - ' . $log->getTitle() . ' ' . $addedText[1] . "\r\n";
                }

                foreach ($subjects[$subject->getSubject()->getId()]['updates']['removed'] as $log) {
                    $updates .= '  - ' . $log->getTitle() . ' ' . $removedText[1] . "\r\n";
                }
            }

            if ($updates != '') {
                $mail = new Mail();
                $mail->setEncoding('UTF-8')
                    ->setBody(str_replace('{{ updates }}', $updates, $message))
                    ->setFrom($mailAddress, $mailName)
                    ->addTo($subscription->getPerson()->getEmail(), $subscription->getPerson()->getFullName())
                    ->addCc($mailAddress, $mailName)
                    ->setSubject($mailSubject);

                if ($this->getEntityManager()->getRepository('Common\Entity\General\Config')                    ->getConfigValue('cudi.catalog_update_mail_to_sysadmin')) {
                    $mail->addBcc(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('system_administrator_mail'),
                        'System Administrator'
                    );
                }

                if ($sendMails) {
                    $this->getMailTransport()
                        ->send($mail);
                }
                $counter++;
            }
        }

        if ($sendMails) {
            $this->writeln('<comment>' . $counter . '</comment> mails have been sent');
        } else {
            $this->writeln('<comment>' . $counter . '</comment> mails would have been sent');
        }
    }
}
