<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace MailBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    MailBundle\Form\Admin\Study\Mail as MailForm,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\Mail\Message,
    Zend\Mime\Part,
    Zend\Mime\Mime,
    Zend\Mime\Message as MimeMessage,
    Zend\Validator\File\Count as CountValidator,
    Zend\Validator\File\Size as SizeValidator,
    Zend\View\Model\ViewModel;


/**
 * StudyController
 *
 * @autor Niels Avonds <niels.avonds@litus.cc>>
 */
class StudyController extends \MailBundle\Component\Controller\AdminController
{
    public function sendAction()
    {
        $currentYear = $this->getCurrentAcademicYear(false);

        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllParentsByAcademicYear($currentYear);

        $groups = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findAll();

        $storedMessages = $this->getDocumentManager()
            ->getRepository('MailBundle\Document\Message')
            ->findAll(array(), array('creationTime' => 'DESC'));

        $form = new MailForm($studies, $groups, $storedMessages);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $upload = new FileUpload(array('ignoreNoFile' => true));

                $upload->addValidator(new SizeValidator(array('max' => '50MB')));

                if ($upload->isValid()) {
                    $enrollments = array();

                    $studyIds = $formData['studies'];

                    if ($studyIds) {
                        foreach ($studyIds as $studyId) {
                            $study = $this->getEntityManager()
                                ->getRepository('SyllabusBundle\Entity\Study')
                                ->findOneById($studyId);

                            $children = $study->getAllChildren();

                            foreach ($children as $child) {
                                $enrollments = array_merge($enrollments, $this->getEntityManager()
                                    ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                                    ->findAllByStudyAndAcademicYear($child, $currentYear));
                            }

                            $enrollments = array_merge($enrollments, $this->getEntityManager()
                                ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                                ->findAllByStudyAndAcademicYear($study, $currentYear));
                        }
                    }

                    $groupIds = $formData['groups'];
                    $extraMembers = array();
                    $excludedMembers = array();

                    if ($groupIds) {
                        foreach ($groupIds as $groupId) {
                            $group = $this->getEntityManager()
                                ->getRepository('SyllabusBundle\Entity\Group')
                                ->findOneById($groupId);

                            $groupExtraMembers = unserialize($group->getExtraMembers());
                            if ($groupExtraMembers)
                                $extraMembers = array_merge($extraMembers, $groupExtraMembers);

                            $groupExcludedMembers = unserialize($group->getExcludedMembers());
                            if ($groupExcludedMembers)
                                $excludedMembers = array_merge($excludedMembers, $groupExcludedMembers);

                            $studies = $this->getEntityManager()
                                ->getRepository('SyllabusBundle\Entity\StudyGroupMap')
                                ->findAllByGroupAndAcademicYear($group, $this->getCurrentAcademicYear(false));

                            foreach  ($studies as $study) {
                                $children = $study->getStudy()->getAllChildren();

                                foreach ($children as $child) {
                                    $enrollments = array_merge($enrollments, $this->getEntityManager()
                                        ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                                        ->findAllByStudyAndAcademicYear($child, $currentYear)
                                    );
                                }

                                $enrollments = array_merge($enrollments, $this->getEntityManager()
                                    ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                                    ->findAllByStudyAndAcademicYear($study->getStudy(), $currentYear)
                                );
                            }
                        }
                    }

                    $addresses = array();
                    $bccs = preg_split("/[,;\s]+/", $formData['bcc']);
                    foreach($bccs as $bcc)
                        $addresses[$bcc] = $bcc;

                    foreach($extraMembers as $extraMember)
                        $addresses[$extraMember] = $extraMember;

                    foreach($enrollments as $enrollment)
                        $addresses[$enrollment->getAcademic()->getEmail()] = $enrollment->getAcademic()->getEmail();

                    foreach($excludedMembers as $excludedMember) {
                        if (isset($addresses[$excludedMember]))
                            unset($addresses[$excludedMember]);
                    }

                    if ('' == $formData['stored_message']) {
                        $body = $formData['message'];

                        $part = new Part($body);

                        $part->type = Mime::TYPE_TEXT;
                        if ($formData['html'])
                            $part->type = Mime::TYPE_HTML;

                        $part->charset = 'utf-8';
                        $message = new MimeMessage();
                        $message->addPart($part);

                        if ($formData['test']) {
                            $body = '<br/>This email would have been sent to:<br/>';
                            foreach($addresses as $address)
                                $body = $body . $address . '<br/>';

                            $part = new Part($body);
                            $part->type = Mime::TYPE_HTML;
                            $message->addPart($part);
                        }

                        $upload->receive();

                        foreach ($upload->getFileInfo() as $file) {
                            if ($file['size'] === NULL)
                                continue;

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
                        $mail->setBody($message)
                            ->setFrom($formData['from'])
                            ->setSubject($formData['subject']);

                        $mail->addTo($formData['from']);
                    } else {
                        $storedMessage = $this->getDocumentManager()
                            ->getRepository('MailBundle\Document\Message')
                            ->findOneById($formData['stored_message']);

                        $body = $storedMessage->getBody();

                        $part = new Part($body);

                        $part->type = Mime::TYPE_TEXT;
                        if ($storedMessage->getType() == 'html')
                            $part->type = Mime::TYPE_HTML;

                        $part->charset = 'utf-8';
                        $message = new MimeMessage();
                        $message->addPart($part);

                        if ($formData['test']) {
                            $body = '<br/>This email would have been sent to:<br/>';
                            foreach($addresses as $address)
                                $body = $body . $address . '<br/>';

                            $part = new Part($body);
                            $part->type = Mime::TYPE_HTML;
                            $message->addPart($part);
                        }

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
                        $mail->setBody($message)
                            ->setFrom($formData['from'])
                            ->setSubject($storedMessage->getSubject());

                        $mail->addTo($formData['from']);
                    }

                    $i = 0;
                    if (!$formData['test']) {
                        foreach ($addresses as $address) {
                            $i++;
                            $mail->addBcc($address);

                            if (500 == $i) {
                                $i = 0;

                                if ('development' != getenv('APPLICATION_ENV'))
                                    $this->getMailTransport()->send($mail);

                                $mail->setBcc(array());
                            }
                        }
                    }

                    if ('development' != getenv('APPLICATION_ENV'))
                        $this->getMailTransport()->send($mail);

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Success',
                            'The mail was successfully sent!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'mail_admin_study',
                        array(
                            'action' => 'send'
                        )
                    );

                    return new ViewModel();
                } else {
                    $dataError = $upload->getMessages();
                    $error = array();

                    foreach($dataError as $key=>$row)
                        $error[] = $row;

                    $form->setMessages(array('file'=>$error ));
                }
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }
}
