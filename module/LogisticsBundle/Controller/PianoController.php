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

namespace LogisticsBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateInterval,
    DateTime,
    LogisticsBundle\Entity\Reservation\PianoReservation,
    LogisticsBundle\Form\PianoReservation\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PianoController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $form = new AddForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                foreach($form->getWeeks() as $key => $week) {
                    if (isset($formData['submit_' . $key])) {
                        $weekIndex = $key;
                        break;
                    }
                }

                if (isset($weekIndex)) {
                    $piano = $this->getEntityManager()
                        ->getRepository('LogisticsBundle\Entity\Reservation\ReservableResource')
                        ->findOneByName(PianoReservation::PIANO_RESOURCE_NAME);

                    $reservation = new PianoReservation(
                        DateTime::createFromFormat('D d#m#Y H#i', $formData['start_date_' . $weekIndex]),
                        DateTime::createFromFormat('D d#m#Y H#i', $formData['end_date_' . $weekIndex]),
                        $piano,
                        '',
                        $this->getAuthentication()->getPersonObject(),
                        $this->getAuthentication()->getPersonObject()
                    );

                    $startWeek = new DateTime();
                    $startWeek->setISODate($reservation->getStartDate()->format('Y'), $weekIndex)
                        ->setTime(0, 0);
                    $endWeek = clone $startWeek;
                    $endWeek->add(new DateInterval('P7D'));

                    $otherReservations = $this->getEntityManager()
                        ->getRepository('LogisticsBundle\Entity\Reservation\PianoReservation')
                        ->findAllConfirmedByDatesAndPerson($startWeek, $endWeek, $this->getAuthentication()->getPersonObject());

                    if (sizeof($otherReservations) == 0) {
                        $reservation->setConfirmed();
                    }

                    $this->getEntityManager()->persist($reservation);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Success',
                            'The reservation was succesfully created!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'logistics_piano',
                        array(
                            'action' => 'index',
                        )
                    );

                    return new ViewModel();
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