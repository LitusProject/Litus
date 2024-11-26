<?php

namespace CudiBundle\Controller\Admin\Sale\Session;

use CudiBundle\Entity\Sale\Session\OpeningHour;
use DateTime;
use Laminas\View\Model\ViewModel;

/**
 * OpeningHourController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class OpeningHourController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour')
                ->findAllActiveQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour')
                ->findAllOldQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('cudi_sale_session_opening-hour_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The opening hour was successfully added!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_session_openinghour',
                    array(
                        'action' => 'manage',
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

    public function editAction()
    {
        $openingHour = $this->getOpeningHourEntity();
        if ($openingHour === null) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_sale_session_opening-hour_edit', $openingHour);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The opening hour was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_session_openinghour',
                    array(
                        'action' => 'manage',
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

    public function scheduleAction()
    {
        $form = $this->getForm('cudi_sale_session_opening-hour_schedule');
        $shiftForm = $this->getForm('shift_shift_add');
        $registrationForm = $this->getForm('shift_registration-shift_add');

        $now = (new DateTime())->format('d/m/Y H:i');

        $monday = new DateTime();                                                                   // create DateTime object with current time
        $monday->setISODate($monday->format('o'), $monday->format('W') + 1);     // set object to Monday on next week

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();
                foreach ($formData as $formKey => $formValue) {
                    $split = explode('_', $formKey);
                    if ($split[0] == 'interval' && $formValue) {
                        $openingTime = $split[1]; // 'noon' of 'evening'
                        if ($openingTime == 'noon') {
                            $startHour = '12:35';
                            $endHour = '13:55';
                            $shiftStartHour = '12:30';
                            $shiftEndHour = '14:00';
                        } else {
                            $startHour = '18:05';
                            $endHour = '19:00';
                            $shiftStartHour = '18:00';
                            $shiftEndHour = '19:00';
                        }

                        $startDate = $split[2] . ' ' . $startHour;
                        $endDate = $split[2] . ' ' . $endHour;
                        $shiftStartDate = $split[2] . ' ' . $shiftStartHour;
                        $shiftEndDate = $split[2] . ' ' . $shiftEndHour;

                        $reward = $openingTime == 'noon' ? 2 : 1; // 1 of 2 shiftersbonnen adhv middag- of avondshift
                        $signoutDate = DateTime::createFromFormat('d/m/Y', $split[2])->modify('+1 day')->format('d/m/Y') . ' 00:00';

                        $data = array( //opening uren en registratie shiften
                            // OPENING HOURS
                            'start_date'        => $startDate,
                            'end_date'          => $endDate,
                            // REGISTRATION SHIFTS
                            'name'              => 'Boekenverkoop',
                            'description'       => 'Kom samen met ons de cursusdienst openhouden en leer ondertussen veel nieuwe mensen kennen! Er is altijd begeleiding aanwezig dus geen enkel probleem als je voor de eerste keer komt ;))',
                            'visible_date'      => $now,
                            'signout_date'      => $signoutDate,
                            'nb_registered'     => $formData['nb-registered_' . $openingTime . '_' . $split[2]],
                            'members_only'      => false,
                            'members_visible'   => true,
                            'final_signin_date' => $endDate,
                            'is_cudi_timeslot'  => true,
                            'manager'           => false,
                            'unit'              => 1,
                            'edit_roles'        => array('cursusdienst',),
                            'event'             => '',
                            'location'          => 1,
                            'handled_on_event'  => false,
                            'ticket_needed'     => false,
                            'points'            => 0,
                        );

                        $shiftData = array( // enkel shiften
                            // OPENING HOURS
                            'start_date'        => $shiftStartDate,
                            'end_date'          => $shiftEndDate,
                            // SHIFTS
                            'name'              => 'Boeken verkopen',
                            'description'       => 'Kom je boeken ophalen ;)',
                            'manager'           => false,
                            'unit'              => 1,
                            'edit_roles'        => array('cursusdienst',),
                            'event'             => '',
                            'location'          => 1,
                            'nb_responsibles'   => 0,
                            'nb_volunteers'     => $formData['volunteers_' . $openingTime . '_' . $split[2]],
                            'nb_volunteers_min' => $formData['volunteers-min_' . $openingTime . '_' . $split[2]],
                            'reward'            => $reward,
                            'handled_on_event'  => false,
                            'ticket_needed'     => true,
                            'points'            => 0,
                        );

                        // OPENING HOURS
                        $this->getEntityManager()->persist(
                            $form->getHydrator()->hydrate($data) //send opening hours data to database
                        );
                        // SHIFTS
                        $this->getEntityManager()->persist(
                            $shiftForm->getHydrator()->hydrate($shiftData) //send Shift DATA to database
                        );

                        // REGISTRATION SHIFTS
                        $count = 0;
                        $startHour_ = $startHour;       // creating dummy variable that is updated
                        while ($count != 5 && $startHour_ != $endHour) {
                            $nextTime = $this->calculateNextTime($startHour_, $endHour);
                            $data['start_date'] = $split[2] . ' ' . $startHour_;
                            $data['end_date'] = $split[2] . ' ' . $nextTime;

                            $this->getEntityManager()->persist(
                                $registrationForm->getHydrator()->hydrate($data) // send registration shift data to database
                            );

                            $startHour_ = $nextTime;
                            $count += 1;
                        }
                    }
                }

                $this->getEntityManager()->flush(); // finalize all database changes

                $this->flashMessenger()->success(
                    'Succes',
                    'This schedule was successfully added!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_session_openinghour',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'       => $form,
                'nextMonday' => $monday,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $openingHour = $this->getOpeningHourEntity();
        if ($openingHour === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($openingHour);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return OpeningHour|null
     */
    private function getOpeningHourEntity()
    {
        $openingHour = $this->getEntityById('CudiBundle\Entity\Sale\Session\OpeningHour');

        if (!($openingHour instanceof OpeningHour)) {
            $this->flashMessenger()->error(
                'Error',
                'No opening hour was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_session_openinghour',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $openingHour;
    }

    /**
     * @return string
     */
    private function calculateNextTime($time, $endTime) //TODO generalize this function to work in all cases
    {
        $hour = explode(':', $time)[0];
        $minute = explode(':', $time)[1];
        $endHour = explode(':', $endTime)[0];
        $endMinute = explode(':', $endTime)[1];
        if ($minute == '00' or $minute == '05') {
            $minute = '30';
        } else {
            $hour = strval($hour + 1);
            $minute = '00';
        }

        // als $hour : $minute groter is dan $endTime dan return $endTime
        if ($hour > $endHour or ($hour == $endHour and $minute >= $endMinute)) {
            return $endTime;
        } else {
            return $hour . ':' . $minute;
        }
    }
}
