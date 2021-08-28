<?php

namespace MailBundle\Controller\Admin;

use CommonBundle\Entity\General\AcademicYear;
use Laminas\Mail\Message;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;
use Laminas\View\Model\ViewModel;

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
                $mail->setEncoding('UTF-8')
                    ->setBody($message)
                    ->setFrom($formData['from'])
                    ->setSubject($formData['subject']);

                $mail->addTo($formData['from']);

                $volunteers = $this->getVolunteers($formData['minimum_rank'], $academicYear);

                foreach ($volunteers as $volunteer) {
                    $mail->addBcc($volunteer->getEmail(), $volunteer->getFullName());
                }

                if (getenv('APPLICATION_ENV') != 'development') {
                    $this->getMailTransport()->send($mail);
                }

                $this->flashMessenger()->success(
                    'Success',
                    'The mail was successfully sent!'
                );

                $this->redirect()->toRoute(
                    'mail_admin_volunteer',
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
     * @param  string       $minRank
     * @param  AcademicYear $academicYear
     * @return array
     */
    private function getVolunteers($minRank, AcademicYear $academicYear)
    {
        $rankingCriteria = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.ranking_criteria')
        );

        if ($minRank == 'none') {
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

            if (!$person->isPraesidium($academicYear)) {
                $list[] = $person;
            }
        }

        return $list;
    }
}
