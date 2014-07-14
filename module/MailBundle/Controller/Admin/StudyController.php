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

use Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\InputFilter\InputInterface,
    Zend\Mail\Message,
    Zend\Mime\Part,
    Zend\Mime\Mime,
    Zend\Mime\Message as MimeMessage,
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

        $form = $this->getForm('mail_study_mail', array('academicYear' => $currentYear));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $upload = new FileUpload(array('ignoreNoFile' => true));
                $inputFilter = $form->getInputFilter()->get('compose_message')->get('file');
                if ($inputFilter instanceof InputInterface)
                    $upload->setValidators($inputFilter->getValidatorChain()->getValidators());

                if ($upload->isValid()) {
                    $addresses = $this->_getAddresses($formData['studies'], $formData['groups'], $formData['bcc']);

                    if ('' == $formData['select_message']['stored_message']) {
                        $body = $formData['compose_message']['message'];

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
                            ->setSubject($formData['compose_message']['subject']);

                        $mail->addTo($formData['from']);
                    } else {
                        $storedMessage = $this->getDocumentManager()
                            ->getRepository('MailBundle\Document\Message')
                            ->findOneById($formData['select_message']['stored_message']);

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

                    $this->flashMessenger()->success(
                        'Success',
                        'The mail was successfully sent!'
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

    private function _getAddresses($studyIds, $groupIds, $bcc)
    {
        $studyEnrollments = $this->_getStudyEnrollments($studyIds);
        list($groupEnrollments, $extraMembers, $excludedMembers) = $this->_getGroupEnrollments($groupIds);

        $enrollments = array_merge($studyEnrollments, $groupEnrollments);

        $addresses = array();
        $bccs = preg_split("/[,;\s]+/", $bcc);
        foreach($bccs as $bcc)
            $addresses[$bcc] = $bcc;

        foreach($extraMembers as $extraMember)
            $addresses[$extraMember] = $extraMember;

        foreach($enrollments as $enrollment)
            $addresses[$enrollment->getAcademic()->getEmail()] = $enrollment->getAcademic()->getEmail();

        foreach ($excludedMembers as $excludedMember) {
            if (isset($addresses[$excludedMember]))
                unset($addresses[$excludedMember]);
        }

        return $addresses;
    }

    private function _getStudyEnrollments($studyIds)
    {
        if (empty($studyIds))
            return array();

        $currentYear = $this->getCurrentAcademicYear(false);

        $enrollments = array();

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

        return $enrollments;
    }

    private function _getGroupEnrollments($groupIds)
    {
        if (empty($groupIds))
            return array();

        $currentYear = $this->getCurrentAcademicYear(false);

        $enrollments = array();
        $extraMembers = array();
        $excludedMembers = array();

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

            foreach ($studies as $study) {
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

        return array($enrollments, $extraMembers, $excludedMembers);
    }
}
