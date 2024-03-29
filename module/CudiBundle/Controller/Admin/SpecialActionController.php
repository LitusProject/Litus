<?php

namespace CudiBundle\Controller\Admin;

use CudiBundle\Component\Mail\Booking as BookingMail;
use CudiBundle\Entity\Sale\Booking;
use Laminas\View\Model\ViewModel;

/**
 * SpecialActionController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SpecialActionController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        return new ViewModel();
    }

    public function irreeelAction()
    {
        $form = $this->getForm('cudi_special-action_irreeel_assign');

        $academicYear = $this->getCurrentAcademicYear();

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $number = 0;
                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($formData['article']['id']);

                $criteria = array('academicYear' => $academicYear->getId());

                if ($formData['only_cudi']) {
                    $criteria['irreeelAtCudi'] = true;
                }

                $people = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
                    ->findBy($criteria);

                foreach ($people as $person) {
                    $registration = $this->getEntityManager()
                        ->getRepository('SecretaryBundle\Entity\Registration')
                        ->findOneByAcademic($person->getAcademic());
                    if ($registration === null) {
                        continue;
                    }

                    if ($person->getAcademic()->isMember($academicYear) && $registration->hasPayed()) {
                        $booking = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sale\Booking')
                            ->findOneSoldOrAssignedOrBookedByArticleAndPersonInAcademicYear(
                                $article,
                                $person->getAcademic(),
                                $academicYear
                            );

                        if ($booking === null) {
                            $number++;
                            $booking = new Booking($this->getEntityManager(), $person->getAcademic(), $article, 'assigned', 1, true);
                            $this->getEntityManager()->persist($booking);

                            if (!$formData['test'] && $formData['send_mail']) {
                                BookingMail::sendAssignMail($this->getEntityManager(), $this->getMailTransport(), array($booking), $booking->getPerson());
                            }
                        } elseif ($booking->getStatus() == 'booked') {
                            $number++;
                            $booking->setStatus('assigned', $this->getEntityManager());
                        }
                    }
                }

                if (!$formData['test']) {
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'There are <b>' . $number . '</b> ' . $article->getMainArticle()->getTitle() . ' assigned!'
                    );

                    $this->redirect()->toRoute(
                        'cudi_admin_special_action',
                        array(
                            'action' => 'manage',
                        )
                    );

                    return new ViewModel(
                        array(
                            'currentAcademicYear' => $academicYear,
                        )
                    );
                } else {
                    $this->getEntityManager()->clear();

                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'There are <b>' . $number . '</b> ' . $article->getMainArticle()->getTitle() . ' that would be assigned!'
                    );

                    $this->redirect()->toRoute(
                        'cudi_admin_special_action',
                        array(
                            'action' => 'irreeel',
                        )
                    );

                    return new ViewModel(
                        array(
                            'currentAcademicYear' => $academicYear,
                        )
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'form'                => $form,
                'currentAcademicYear' => $academicYear,
            )
        );
    }
}
