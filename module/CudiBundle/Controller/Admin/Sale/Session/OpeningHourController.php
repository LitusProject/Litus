<?php

namespace CudiBundle\Controller\Admin\Sale\Session;

use DateTime;
use CudiBundle\Entity\Sale\Session\OpeningHour;
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
                    $split = explode("_", $formKey);
                    if ($split[0] == 'interval' && $formValue) {
                        $startHour = explode('-', $split[1])[0];
                        $endHour = explode('-', $split[1])[1];
                        $startDate = $split[2] . ' ' . $startHour;
                        $endDate = $split[2] . ' ' . $endHour;

                        $reward = $startHour == '12:30'? 2: 1;
                        $signoutDate = (DateTime::createFromFormat('d/m/Y', $split[2]))->modify('+1 day')->format('d/m/Y') . ' 00:00';

                        $data = array(
                            // OPENING HOURS
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            // SHIFTS
                            'name'                  => 'Boekjes verkopen',
                            'description'           => 'Kom helpen met boeken verkopen. Leer nieuwe mensen kennen en kom de sfeer opsnuiven.
(Er is altijd begeleiding dus wees niet bang als je voor de eerste keer komt ;))',
                            'manager'               => false,
                            'unit'                  => 1,
                            'edit_roles'            => array('cursusdienst',),
                            'event'                 => '',
                            'location'              => 1,
                            'nb_responsibles'       => 0,
                            'nb_volunteers'         => $formData['volunteers_' . $startHour . '-' . $endHour . '_' . $split[2]],
                            'nb_volunteers_min'     => $formData['volunteers-min_' . $startHour . '-' . $endHour . '_' . $split[2]],
                            'reward'                => $reward,
                            'handled_on_event'      => false,
                            'ticket_needed'         => false,
                            'points'                => 0,
                            // REGISTRATION SHIFTS
                            'visible_date' => $now,
                            'signout_date' => $signoutDate,
                            'nb_registered' => $formData['nb-registered_' . $startHour . '-' . $endHour . '_' . $split[2]],
                            'members_only' => false,
                            'members_visible' => true,
                            'final_signin_date' => $startDate,
                            'is_cudi_timeslot' => true,
                        );

                        // OPENING HOURS
                        $this->getEntityManager()->persist(
                            $form->getHydrator()->hydrate($data)
                        );
                        // SHIFTS
                        $this->getEntityManager()->persist(
                            $shiftForm->getHydrator()->hydrate($data)
                        );

                        // REGISTRATION SHIFTS
                        $data['name'] = 'Boekenverkoop';
                        $data['description'] = 'Kom je boeken ophalen ;)';
                        $data['ticket_needed'] = true;
                        $count = 0;
                        $startHour_ = $startHour;       // creating dummy variable that is updated
                        while ($count != 5 && $startHour_ != $endHour) {
                            $nextTime = $this->calculateNextTime($startHour_);
                            $data['start_date'] = $split[2] . ' ' . $startHour_;
                            $data['end_date'] = $split[2] . ' ' . $nextTime;

                            $this->getEntityManager()->persist(
                                $registrationForm->getHydrator()->hydrate($data)
                            );

                            $startHour_ = $nextTime;
                            $count += 1;
                        }
                    }
                }

                $this->getEntityManager()->flush();

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
                'form'          => $form,
                'nextMonday'    => $monday,
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
    private function calculateNextTime($time)
    {
        $hour = explode(':', $time)[0];
        $minute = explode(':', $time)[1];
        if ($minute == '00') {
            $minute = '30';
        } else {
            $hour = strval($hour + 1);
            $minute = '00';
        }

        return $hour . ':' . $minute;
    }
}
