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

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part;
use Zend\View\Model\ViewModel;

/**
 * PromotionController
 *
 * @autor Pieter Maene <pieter.maene@litus.cc>>
 */
class PromotionController extends \MailBundle\Component\Controller\AdminController
{
    public function sendAction()
    {
        $form = $this->getForm('mail_promotion_mail');

        if ($this->getRequest()->isPost()) {
            $form->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );

            if ($form->isValid()) {
                $formData = $form->getData();

                $people = array();
                $enrollments = array();
                $groupIds = $formData['groups'] ?? null;

                $addresses = array();

                foreach ($formData['to'] as $to) {
                    $academicYear = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\AcademicYear')
                        ->findOneById($to);

                    if ($groupIds) {
                        foreach ($groupIds as $groupId) {
                            $group = $this->getEntityManager()
                                ->getRepository('SyllabusBundle\Entity\Group')
                                ->findOneById($groupId);

                            $studies = $this->getEntityManager()
                                ->getRepository('SyllabusBundle\Entity\Group\StudyMap')
                                ->findAllByGroupAndAcademicYear($group, $academicYear);

                            foreach ($studies as $study) {
                                if ($study->getStudy()->getPhase() == 2) {
                                    $enrollments = array_merge(
                                        $enrollments,
                                        $this->getEntityManager()
                                            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                                            ->findAllByStudy($study->getStudy())
                                    );
                                }
                            }
                        }

                        foreach ($enrollments as $enrollment) {
                            if ($enrollment->getAcademic()->getPersonalEmail() !== null) {
                                array_push($addresses, $enrollment->getAcademic()->getPersonalEmail());
                            }
                        }
                    } else {
                        $people = array_merge(
                            $people,
                            $this->getEntityManager()
                                ->getRepository('SecretaryBundle\Entity\Promotion')
                                ->findAllByAcademicYear($academicYear)
                        );

                        foreach ($people as $person) {
                            if ($person->getEmailAddress() !== null) {
                                array_push($addresses, $person->getEmailAddress());
                            }
                        }
                    }
                }

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('secretary.mail_name');

                $from = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('secretary.mail');

                if ($formData['selected_message']['stored_message'] == '') {
                    $body = $formData['compose_message']['message'];

                    $part = new Part($body);

                    $part->type = Mime::TYPE_TEXT;
                    if ($formData['html']) {
                        $part->type = Mime::TYPE_HTML;
                    }

                    $part->charset = 'utf-8';
                    $message = new MimeMessage();
                    $message->addPart($part);

                    foreach ($formData['compose_message']['file'] as $file) {
                        if (!$file['size']) {
                            continue;
                        }

                        $part = new Part(fopen($file['tmp_name'], 'r'));
                        $part->type = $file['type'];
                        $part->id = $file['name'];
                        $part->disposition = 'attachment';
                        $part->filename = $file['name'];
                        $part->encoding = Mime::ENCODING_BASE64;

                        unlink($file['tmp_name']);

                        $message->addPart($part);
                    }

                    $mail = new Message();
                    $mail->setEncoding('UTF-8')
                        ->setBody($message)
                        ->setFrom($from, $mailName)
                        ->addTo($from, $mailName)
                        ->setSubject($formData['compose_message']['subject']);
                } else {
                    $storedMessage = $this->getDocumentManager()
                        ->getRepository('MailBundle\Document\Message')
                        ->findOneById($formData['selected_message']['stored_message']);

                    $body = $storedMessage->getBody();

                    $part = new Part($body);

                    $part->type = Mime::TYPE_TEXT;
                    if ($storedMessage->getType() == 'html') {
                        $part->type = Mime::TYPE_HTML;
                    }

                    $part->charset = 'utf-8';
                    $message = new MimeMessage();
                    $message->addPart($part);

                    foreach ($storedMessage->getAttachments() as $attachment) {
                        $part = new Part($attachment->getData());
                        $part->type = $attachment->getContentType();
                        $part->id = $attachment->getFilename();
                        $part->disposition = 'attachment';
                        $part->filename = $attachment->getFilename();
                        $part->encoding = Mime::ENCODING_BASE64;

                        $message->addPart($part);
                    }

                    $mail = new Message();
                    $mail->setEncoding('UTF-8')
                        ->setBody($message)
                        ->setFrom($from, $mailName)
                        ->addTo($from, $mailName)
                        ->setSubject($storedMessage->getSubject());
                }

                $bccs = preg_split('/[,;\s]+/', $formData['bcc']);
                foreach ($bccs as $bcc) {
                    $mail->addBcc($bcc);
                }
                $i = 0;
                $uniqueAddresses = array_unique($addresses);

                if ($formData['test']) {
                    $body = '<br/>This email would have been sent to:<br/>';
                    foreach ($addresses as $address) {
                        $body .= $address . '<br/>';
                    }
                    $part = new Part($body);
                    $part->type = Mime::TYPE_HTML;
                    $message->addPart($part);
                } else {
                    foreach ($uniqueAddresses as $address) {
                        $i++;
                        $mail->addBcc($address);

                        if ($i == 500) {
                            $i = 0;
                            if (getenv('APPLICATION_ENV') != 'development') {
                                $this->getMailTransport()->send($mail);
                            }

                            $mail->setBcc(array());
                        }
                    }
                }

                if (getenv('APPLICATION_ENV') != 'development') {
                    $this->getMailTransport()->send($mail);
                }

                $this->flashMessenger()->success(
                    'Success',
                    'The mail was successfully sent!'
                );

                $this->redirect()->toRoute(
                    'mail_admin_promotion',
                    array(
                        'action' => 'send',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }
}
