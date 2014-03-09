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

use DateTime,
    DateInterval,
    CommonBundle\Component\Util\AcademicYear as AcademicYearUtil,
    CommonBundle\Entity\General\AcademicYear;

/**
 * Updates catalog
 */
class CatalogUpdate extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this
            ->setName('cudi:catalog:update')
            ->setAliases(array('cudi:update-catalog'))
            ->setDescription('Update the catalog.')
            ->addOption('mail', 'm', null, 'Send mails to users to notify them of the update.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command updates the catalog and notifies the users of the changes.
EOT
        );
    }

    protected function executeCommand()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));

        $academicYear = $this->_getCurrentAcademicYear();
        $subjects = array();

        $this->_findAllBookable($subjects, $date, $academicYear);
        $this->_findAllUnbookable($subjects, $date, $academicYear);
        $this->_findAllAdded($subjects, $date, $academicYear);
        $this->_findAllRemoved($subjects, $date, $academicYear);

        $this->writeln('A total of <comment>' . count($subjects) . '</comment> subjects is affected.');

        $this->_notifySubscribers($subjects, $academicYear);
    }

    protected function getLogName()
    {
        return 'CatalogUpdate';
    }

    private function _findAllBookable(array $subjects, DateTime $date, AcademicYear $academicYear)
    {
        $logs = $this->getEntityManager()->getRepository('CudiBundle\Entity\Log\Article\Sale\Bookable')
            ->findAllAfter($date);
        $this->writeln('Found <comment>' . count($logs) . '</comment> log entries for Bookable.');

        foreach ($logs as $log) {
            $article = $log->getArticle($this->getEntityManager());

            $mappings = $this->getEntityManager()->getRepository('CudiBundle\Entity\Article\SubjectMap')
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
    }

    private function _findAllUnbookable(array $subjects, DateTime $date, AcademicYear $academicYear)
    {
        $logs = $this->getEntityManager()->getRepository('CudiBundle\Entity\Log\Article\Sale\Unbookable')
            ->findAllAfter($date);
        $this->writeln('Found <comment>' . count($logs) . '</comment> log entries for Unbookable.');

        foreach ($logs as $log) {
            $article = $log->getArticle($this->getEntityManager());

            $mappings = $this->getEntityManager()->getRepository('CudiBundle\Entity\Article\SubjectMap')
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
    }

    private function _findAllAdded(array $subjects, DateTime $date, AcademicYear $academicYear)
    {
        $logs = $this->getEntityManager()->getRepository('CudiBundle\Entity\Log\Article\SubjectMap\Added')
            ->findAllAfter($date);
        $this->writeln('Found <comment>' . count($logs) . '</comment> log entries for Added articles.');

        foreach ($logs as $log) {
            $subjectMap= $log->getSubjectMap($this->getEntityManager());

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
    }

    private function _findAllRemoved(array $subjects, DateTime $date, AcademicYear $academicYear)
    {
        $logs = $this->getEntityManager()->getRepository('CudiBundle\Entity\Log\Article\SubjectMap\Removed')
            ->findAllAfter($date);
        $this->writeln('Found <comment>' . count($logs) . '</comment> log entries for Removed articles.');

        foreach ($logs as $log) {
            $subjectMap= $log->getSubjectMap($this->getEntityManager());

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
    }

    private function _notifySubscribers(array $subjects, AcademicYear $academicYear)
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
        if ($sendMails && 'development' == getenv('APPLICATION_ENV')) {
            $sendMails = false;
            $this->writeln('<error>WARNING:</error> The mails will not be sent because the application is running in development mode.');
        }

        foreach ($subscribers as $subscription) {
            $academicSubjects = $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
                ->findAllByAcademicAndAcademicYear($subscription->getPerson(), $academicYear);

            if (!($language = $subscription->getPerson()->getLanguage())) {
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
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('system_administrator_mail'),
                        'System Administrator'
                    )
                    ->setSubject($mailSubject);

                if ($sendMails) {
                    $this->getMailTransport()
                        ->send($mail);
                }
                $counter++;
            }
        }

        if ($sendMails)
            $this->writeln('<comment>' . $counter . '</comment> mails have been sent.');
        else
            $this->writeln('<comment>' . $counter . '</comment> mails would have been sent.');
    }

    private function _getCurrentAcademicYear()
    {
        $startAcademicYear = AcademicYearUtil::getStartOfAcademicYear();
        $startAcademicYear->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);

        return $academicYear;
    }
}
