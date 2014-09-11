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

use MailBundle\Form\Admin\Promotion\Mail as MailForm,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * PromotionController
 *
 * @autor Pieter Maene <pieter.maene@litus.cc>>
 */
class PromotionController extends \MailBundle\Component\Controller\AdminController
{
    public function sendAction()
    {
        $from = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.mail');

        $results = array();

        $groups = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findAll();

        $form = new MailForm($this->getEntityManager(), $groups);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $people = array();
                $enrollments = array();
                $groupIds = $formData['groups'];

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
                                ->getRepository('SyllabusBundle\Entity\StudyGroupMap')
                                ->findAllByGroupAndAcademicYear($group, $academicYear);

                            foreach ($studies as $study) {
                                if($study->getStudy()->getPhase() == 2){
                                    $children = $study->getStudy()->getAllChildren();

                                    foreach ($children as $child) {
                                        $enrollments = array_merge($enrollments, $this->getEntityManager()
                                            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                                            ->findAllByStudyAndAcademicYear($child, $academicYear)
                                        );
                                    }

                                    $enrollments = array_merge($enrollments, $this->getEntityManager()
                                        ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                                        ->findAllByStudyAndAcademicYear($study->getStudy(), $academicYear)
                                    );
                                }
                            }
                        }
                    }
                    else {
                        $people = array_merge(
                            $people,
                            $this->getEntityManager()
                                ->getRepository('SecretaryBundle\Entity\Promotion')
                                ->findAllByAcademicYear($academicYear)
                        );
                    }
                }

                foreach($enrollments as $enrollment)
                        array_push($people, $enrollment->getAcademic());

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('secretary.mail_name');

                $mail = new Message();
                $mail->setBody($formData['message'])
                    ->setFrom($from, $mailName)
                    ->addTo($from, $mailName)
                    ->setSubject($formData['subject']);

                $i = 0;
                foreach ($people as $person) {
                    if (null !== $person->getEmailAddress()) {
                        $i++;
                        $mail->addBcc($person->getEmailAddress(), $person->getFullName());
                    }

                    if ($i == 500) {
                        $i = 0;
                        if ('development' != getenv('APPLICATION_ENV'))
                            $this->getMailTransport()->send($mail);

                        $mail->setBcc(array());
                    }
                }

                if ('development' != getenv('APPLICATION_ENV'))
                    $this->getMailTransport()->send($mail);

                $this->flashMessenger()->success(
                    'Success',
                    'The mail was successfully sent!'
                );

                $this->redirect()->toRoute(
                    'mail_admin_promotion',
                    array(
                        'action' => 'send'
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
