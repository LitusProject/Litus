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

namespace MailBundle\Controller\Admin;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person,
    DateTime,
    Markdown_Parser,
    Zend\Mail\Message,
    Zend\Mime\Message as MimeMessage,
    Zend\Mime\Mime,
    Zend\Mime\Part,
    Zend\View\Model\ViewModel;

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

                $this->saveConfig($formData['subject'], $formData['message']);

                foreach ($statuses as $status) {
                    if ('' == $status->getPerson()->getEmail()) {
                        continue;
                    }

                    if (!($subjects = $this->getSubjects($status->getPerson(), $academicYear, $semester))) {
                        continue;
                    }

                    $body = str_replace('{{ subjects }}', $subjects, $formData['message']);

                    $parser = new Markdown_Parser();
                    $body = nl2br($parser->transform($body));
                    $body = preg_replace('/<([a-z\/]+)><br \/>\n<br \/>\n<([a-z]+)>/is', '<$1><$2>', $body);
                    $body = preg_replace('/<([a-z\/]+)><br \/>\n<([a-z]+)>/is', '<$1><$2>', $body);
                    $part = new Part($body);

                    $part->type = Mime::TYPE_HTML;

                    $part->charset = 'utf-8';
                    $message = new MimeMessage();
                    $message->addPart($part);

                    $mail = new Message();
                    $mail->setBody($message)
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
                            $status->getPerson()->getEmail(), $status->getPerson()->getFullName()
                        );
                    }

                    if ('development' != getenv('APPLICATION_ENV')) {
                        $this->getMailTransport()->send($mail);
                    }

                    if ($formData['test_it']) {
                        break;
                    }
                }

                $this->flashMessenger()->success(
                    'Success',
                    'The mail was successfully sent!'
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
                'form' => $form,
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
     * @param  int          $semester
     * @return string|null
     */
    private function getSubjects(Person $person, AcademicYear $academicYear, $semester)
    {
        $allSubjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findAllByProfAndAcademicYear($person, $academicYear);

        $subjects = array();
        foreach ($allSubjects as $subject) {
            if ($subject->getSubject()->getSemester() == $semester || $subject->getSubject()->getSemester() == 3) {
                $subjects[] = $subject->getSubject();
            }
        }

        if (empty($subjects)) {
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
