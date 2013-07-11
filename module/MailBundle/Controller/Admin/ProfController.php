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
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    DateInterval,
    DateTime,
    MailBundle\Form\Admin\Cudi\Mail as MailForm,
    Zend\Mail\Message,
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
        $academicYear = $this->_getAcademicYear();

        $semester = (new DateTime() < $academicYear->getUniversityStartDate()) ? 1 : 2;

        $mailSubject = str_replace(
            array(
                '{{ semester }}',
                '{{ academicYear }}'
            ),
            array(
                (1 == $semester ? 'Eerste' : 'Tweede'),
                $academicYear->getCode()
            ),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('mail.start_cudi_mail_subject')
        );

        $mail = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('mail.start_cudi_mail');

        $form = new MailForm();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $mailAddress = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.mail');

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.mail_name');

                $statuses = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Status\University')
                    ->findAllByStatus('professor', $academicYear);

                foreach($statuses as $status) {
                    if ('' == $status->getPerson()->getEmail())
                        continue;

                    $allSubjects = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
                        ->findAllByProfAndAcademicYear($status->getPerson(), $academicYear);

                    $subjects = array();
                    foreach($allSubjects as $subject) {
                        if ($subject->getSubject()->getSemester() == $semester || $subject->getSubject()->getSemester() == 3) {
                            $subjects[] = $subject->getSubject();
                        }
                    }

                    if (empty($subjects))
                        continue;

                    $text = '';
                    for($i = 0; isset($subjects[$i]); $i++) {
                        if ($i != 0)
                             $text .= PHP_EOL;

                        $text .= '    [' . $subjects[$i]->getCode() . '] - ' . $subjects[$i]->getName();
                    }

                    $body = str_replace('{{ subjects }}', $text, $mail);

                    $message = new Message();
                    $message->setBody($body)
                        ->setFrom($mailAddress, $mailName)
                        ->setSubject($mailSubject);

                    $message->addBcc($mailAddress);

                    if ($formData['test_it']) {
                        $message->addTo(
                            $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\General\Config')
                                ->getConfigValue('system_administrator_mail'),
                            'System Administrator'
                        );
                    } else {
                        $message->addTo(
                            $status->getPerson()->getEmail(), $status->getPerson()->getFullName()
                        );
                    }

                    if ('development' != getenv('APPLICATION_ENV'))
                        $this->getMailTransport()->send($message);

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
                'subject' => $mailSubject,
                'mail' => $mail,
                'semester' => $semester,
                'form' => $form,
            )
        );
    }

    private function _getAcademicYear()
    {
        $startAcademicYear = AcademicYear::getStartOfAcademicYear();
        $startAcademicYear->setTime(0, 0);

        $now = new DateTime();
        $profStart = new DateTime(
            str_replace(
                '{{ year }}',
                $startAcademicYear->format('Y'),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.prof_start_academic_year')
            )
        );

        if ($now > $profStart && $startAcademicYear > $now) {
            $startAcademicYear->add(new DateInterval('P1Y2M'));
            $startAcademicYear = AcademicYear::getStartOfAcademicYear($startAcademicYear);
        }

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);

        if (null === $academicYear) {
            $organizationStart = str_replace(
                '{{ year }}',
                $startAcademicYear->format('Y'),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('start_organization_year')
            );
            $organizationStart = new DateTime($organizationStart);
            $academicYear = new AcademicYear($organizationStart, $startAcademicYear);
            $this->getEntityManager()->persist($academicYear);
            $this->getEntityManager()->flush();
        }

        return $academicYear;
    }
}
