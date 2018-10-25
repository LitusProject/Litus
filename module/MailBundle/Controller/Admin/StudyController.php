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
            $form->setData(array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            ));

            if ($form->isValid()) {
                $formData = $form->getData();

                $groups = array();
                if (isset($formData['groups'])) {
                    $groups = $formData['groups'];
                }

                $addresses = $this->getAddresses($formData['studies'], $groups, $formData['bcc']);

                if ('' == $formData['selected_message']['stored_message']) {
                    $body = $formData['compose_message']['message'];

                    $part = new Part($body);

                    $part->type = Mime::TYPE_TEXT;
                    if ($formData['html']) {
                        $part->type = Mime::TYPE_HTML;
                    }

                    $part->charset = 'utf-8';
                    $message = new MimeMessage();
                    $message->addPart($part);

                    if ($formData['test']) {
                        $body = '<br/>This email would have been sent to:<br/>';
                        foreach ($addresses as $address) {
                            $body = $body . $address . '<br/>';
                        }

                        $part = new Part($body);
                        $part->type = Mime::TYPE_HTML;
                        $message->addPart($part);
                    }

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
                        ->setFrom($formData['from'])
                        ->setSubject($formData['compose_message']['subject']);

                    $mail->addTo($formData['from']);
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

                    if ($formData['test']) {
                        $body = '<br/>This email would have been sent to:<br/>';
                        foreach ($addresses as $address) {
                            $body = $body . $address . '<br/>';
                        }

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
                    $mail->setEncoding('UTF-8')
                        ->setBody($message)
                        ->setFrom($formData['from'])
                        ->setSubject($storedMessage->getSubject());

                    $mail->addTo($formData['from']);
                }

                $i = 0;
                if (!$formData['test']) {
                    foreach ($addresses as $address) {
                        if ('' == $address) {
                            continue;
                        }

                        $i++;
                        $mail->addBcc($address);

                        if (500 == $i) {
                            $i = 0;

                            if ('development' != getenv('APPLICATION_ENV')) {
                                $this->getMailTransport()->send($mail);
                            }

                            $mail->setBcc(array());
                        }
                    }
                }

                if ('development' != getenv('APPLICATION_ENV')) {
                    $this->getMailTransport()->send($mail);
                }

                $this->flashMessenger()->success(
                    'Success',
                    'The mail was successfully sent!'
                );

                $this->redirect()->toRoute(
                    'mail_admin_study',
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

    /**
     * @param  array $studyIds
     * @param  array $groupIds
     * @param  array $bcc
     * @return array
     */
    private function getAddresses($studyIds, $groupIds, $bcc)
    {
        $studyEnrollments = $this->getStudyEnrollments($studyIds);
        list($groupEnrollments, $extraMembers, $excludedMembers) = $this->getGroupEnrollments($groupIds);

        $enrollments = array_merge($studyEnrollments, $groupEnrollments);

        $addresses = array();
        $bccs = preg_split('/[,;\s]+/', $bcc);
        foreach ($bccs as $bcc) {
            $addresses[$bcc] = $bcc;
        }

        foreach ($extraMembers as $extraMember) {
            $addresses[$extraMember] = $extraMember;
        }

        foreach ($enrollments as $enrollment) {
            $addresses[$enrollment->getAcademic()->getEmail()] = $enrollment->getAcademic()->getEmail();
        }

        foreach ($excludedMembers as $excludedMember) {
            if (isset($addresses[$excludedMember])) {
                unset($addresses[$excludedMember]);
            }
        }

        return $addresses;
    }

    /**
     * @param  array $studyIds
     * @return array
     */
    private function getStudyEnrollments($studyIds)
    {
        if (empty($studyIds)) {
            return array();
        }

        $enrollments = array();

        foreach ($studyIds as $studyId) {
            $study = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study')
                ->findOneById($studyId);

            $enrollments = array_merge($enrollments, $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                ->findAllByStudy($study));
        }

        return $enrollments;
    }

    /**
     * @param  array $groupIds
     * @return array
     */
    private function getGroupEnrollments($groupIds)
    {
        if (empty($groupIds)) {
            return array(array(), array(), array());
        }

        $enrollments = array();
        $extraMembers = array();
        $excludedMembers = array();

        foreach ($groupIds as $groupId) {
            $group = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Group')
                ->findOneById($groupId);

            $groupExtraMembers = unserialize($group->getExtraMembers());
            if ($groupExtraMembers) {
                $extraMembers = array_merge($extraMembers, $groupExtraMembers);
            }

            $groupExcludedMembers = unserialize($group->getExcludedMembers());
            if ($groupExcludedMembers) {
                $excludedMembers = array_merge($excludedMembers, $groupExcludedMembers);
            }

            $studies = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Group\StudyMap')
                ->findAllByGroupAndAcademicYear($group, $this->getCurrentAcademicYear(false));

            foreach ($studies as $study) {
                $enrollments = array_merge($enrollments, $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                    ->findAllByStudy($study->getStudy())
                );
            }
        }

        return array($enrollments, $extraMembers, $excludedMembers);
    }
}
