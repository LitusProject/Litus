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

namespace MailBundle\Controller\Admin;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person;
use DateInterval;
use DateTime;
use Parsedown;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part;
use Zend\View\Model\ViewModel;

/**
 * ProfController
 *
 * @autor Kristof Mariën <kristof.marien@litus.cc>
 */
class ProfController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function cudiAction()
    {
        $academicYear = $this->getCurrentAcademicYear();

        $semester = (new DateTime() < $academicYear->getUniversityStartDate()) ? 1 : 2;

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('mail.start_cudi_mail')
        );

        $form = $this->getForm('mail_prof_mail', array('subject' => $mailData['subject'], 'message' => $mailData['message'], 'semester' => $semester));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();
                $semester = $formData['semester'];

                $mailAddress = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.mail');

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.mail_name');

                $statuses = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Status\University')
                    ->findAllByStatus('professor', $academicYear);

                $reduced = $formData['reduced_list'];

                $this->saveConfig($formData['subject'], $formData['message']);

                $counter = 0;

                foreach ($statuses as $status) {
                    if ($status->getPerson()->getEmail() == '') {
                        continue;
                    }

                    $subjects = $this->getSubjects($status->getPerson(), $academicYear, $semester, $reduced);
                    if ($subjects === null) {
                        continue;
                    }

                    $body = str_replace('{{ subjects }}', $subjects, $formData['message']);

                    $parsedown = new Parsedown();
                    $body = nl2br($parsedown->text($body));
                    $body = preg_replace('/<([a-z\/]+)><br \/>\n<br \/>\n<([a-z]+)>/is', '<$1><$2>', $body);
                    $body = preg_replace('/<([a-z\/]+)><br \/>\n<([a-z]+)>/is', '<$1><$2>', $body);
                    $part = new Part($body);

                    $part->type = Mime::TYPE_HTML;

                    $part->charset = 'utf-8';
                    $message = new MimeMessage();
                    $message->addPart($part);

                    $mail = new Message();
                    $mail->setEncoding('UTF-8')
                        ->setBody($message)
                        ->setFrom($mailAddress, $mailName)
                        ->setSubject($formData['subject']);

                    $mail->addBcc($mailAddress);

                    if ($formData['test_it']) {
                        $mail->addTo(
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('system_administrator_mail'),
                            'System Administrator'
                        );
                        $mail->addTo(
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('cudi.mail'),
                            'Cudi'
                        );
                    } else {
                        $mail->addTo(
                            $status->getPerson()->getEmail(),
                            $status->getPerson()->getFullName()
                        );
                    }

                    if (getenv('APPLICATION_ENV') != 'development') {
                        $counter++;
                        $this->getMailTransport()->send($mail);
                    }

                    if ($formData['test_it']) {
                        break;
                    }
                }

                $this->flashMessenger()->success(
                    'Success',
                    $counter.' mail(s) was(were) successfully sent!'
                );

                $this->redirect()->toRoute(
                    'mail_admin_prof',
                    array(
                        'action' => 'cudi',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'semester' => $semester,
                'form'     => $form,
            )
        );
    }

    /**
     * @param string $subject
     * @param string $message
     */
    private function saveConfig($subject, $message)
    {
        $config = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->findOneByKey('mail.start_cudi_mail');

        $config->setValue(
            serialize(
                array(
                    'subject' => $subject,
                    'message' => $message,
                )
            )
        );
        $this->getEntityManager()->flush();
    }

    /**
     * @param  Person       $person
     * @param  AcademicYear $academicYear
     * @param  integer      $semester
     * @return string|null
     */
    private function getSubjects(Person $person, AcademicYear $academicYear, $semester, $reduced)
    {
        $allSubjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
            ->findAllByProfAndAcademicYear($person, $academicYear);

        $now = new DateTime();

        $lastAcademicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByDate($now->sub(new DateInterval('P1Y')));

        $subjects = array();
        $subjectIds = array();
        foreach ($allSubjects as $subject) {
            if (!in_array($subject->getSubject()->getId(), $subjectIds)) {
                if ($subject->getSubject()->getSemester() == $semester || $subject->getSubject()->getSemester() == 3) {
                    if ($reduced) {
                        $articles = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                            ->findAllBySubjectAndAcademicYear($subject->getSubject(), $lastAcademicYear);

                        if (count($articles) > 0) {
                            $subjects[] = $subject->getSubject();
                            $subjectIds[] = $subject->getSubject()->getId();                       
                        }
                    } else {
                        $subjects[] = $subject->getSubject();
                        $subjectIds[] = $subject->getSubject()->getId();
                    }
                }
            }
        }

        if (count($subjects) == 0) {
            return null;
        }

        $text = '';
        for ($i = 0; isset($subjects[$i]); $i++) {
            if ($i != 0) {
                $text .= PHP_EOL;
            }

            $text .= '    [' . $subjects[$i]->getCode() . '] - ' . $subjects[$i]->getName();
        }

        return $text;
    }
}
