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
    Zend\Mail\Message,
    Zend\Mime\Part,
    Zend\Mime\Mime,
    Zend\Mime\Message as MimeMessage,
    Zend\View\Model\ViewModel;

/**
 * VolunteerController
 *
 * @autor Niels Avonds <niels.avonds@litus.cc>>
 */
class VolunteerController extends \MailBundle\Component\Controller\AdminController
{
    public function sendAction()
    {
        $academicYear = $this->getCurrentAcademicYear();

        $form = $this->getForm('mail_volunteer_mail');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $body = $formData['message'];

                $part = new Part($body);
                $part->type = Mime::TYPE_TEXT;
                $part->charset = 'utf-8';

                $message = new MimeMessage();
                $message->addPart($part);

                $mail = new Message();
                $mail->setBody($message)
                    ->setFrom($formData['from'])
                    ->setSubject($formData['subject']);

                $mail->addTo($formData['from']);

                $volunteers = $this->_getVolunteers($formData['minimum_rank'], $academicYear);

                foreach ($volunteers as $volunteer) {
                    $mail->addBcc($volunteer->getEmail(), $volunteer->getFullName());
                }

                if ('development' != getenv('APPLICATION_ENV'))
                    $this->getMailTransport()->send($mail);

                $this->flashMessenger()->success(
                    'Success',
                    'The mail was successfully sent!'
                );

                $this->redirect()->toRoute(
                    'mail_admin_volunteer',
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

    private function _getVolunteers($minRank, AcademicYear $academicYear)
    {
        $rankingCriteria = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.ranking_criteria')
        );

        if ('none' == $minRank) {
            $volunteers = $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\Shift\Volunteer')
                ->findAllByCountMinimum($academicYear, 1);
        } else {
            $volunteers = $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\Shift\Volunteer')
                ->findAllByCountMinimum(
                    $academicYear,
                    $rankingCriteria[$minRank]['limit']
                );
        }

        $list = array();
        foreach ($volunteers as $volunteer) {
            $person = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($volunteer['id']);

            if (!$person->isPraesidium($academicYear))
                $list[] = $person;
        }

        return $list;
    }
}
