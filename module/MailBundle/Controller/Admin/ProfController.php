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

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    DateInterval,
    DateTime,
    MailBundle\Form\Admin\Cudi\Mail as MailForm,
    Markdown_Parser,
    Zend\Mail\Message,
    Zend\Mime\Part,
    Zend\Mime\Mime,
    Zend\Mime\Message as MimeMessage,
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

        $mailSubject = $mailData['subject'];
        $message = $mailData['message'];

        $form = new MailForm($mailSubject, $message, $semester);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);
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

                $config = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->findOneByKey('mail.start_cudi_mail');

                $config->setValue(
                    serialize(
                        array(
                            'subject' => $formData['subject'],
                            'message' => $formData['message'],
                        )
                    )
                );
                $this->getEntityManager()->flush();

                foreach ($statuses as $status) {
                    if ('' == $status->getPerson()->getEmail())
                        continue;

                    $allSubjects = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
                        ->findAllByProfAndAcademicYear($status->getPerson(), $academicYear);

                    $subjects = array();
                    foreach ($allSubjects as $subject) {
                        if ($subject->getSubject()->getSemester() == $semester || $subject->getSubject()->getSemester() == 3) {
                            $subjects[] = $subject->getSubject();
                        }
                    }

                    if (empty($subjects))
                        continue;

                    $text = '';
                    for ($i = 0; isset($subjects[$i]); $i++) {
                        if ($i != 0)
                             $text .= PHP_EOL;

                        $text .= '    [' . $subjects[$i]->getCode() . '] - ' . $subjects[$i]->getName();
                    }

                    $body = str_replace('{{ subjects }}', $text, $formData['message']);

                    $parser = new Markdown_Parser();
                    $part = new Part($parser->transform($body));

                    $part->type = Mime::TYPE_TEXT;
                    if ($formData['html'])
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
                    } else {
                        $mail->addTo(
                            $status->getPerson()->getEmail(), $status->getPerson()->getFullName()
                        );
                    }

                    if ('development' != getenv('APPLICATION_ENV'))
                        $this->getMailTransport()->send($mail);

                    if ($formData['test_it'])
                        break;
                }

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The mail was successfully sent!'
                    )
                );

                $this->redirect()->toRoute(
                    'mail_admin_prof',
                    array(
                        'action' => 'cudi'
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
}
