<?php

namespace ShopBundle\Controller\Admin;

use DateTime;
use ShopBundle\Entity\Session\OpeningHour;
use Laminas\View\Model\ViewModel;

/**
 * OpeningHourController
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class OpeningHourController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\Session\OpeningHour')
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
                ->getRepository('ShopBundle\Entity\Session\OpeningHour')
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
        $form = $this->getForm('shop_admin_session_opening-hour_add');

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
                    'shop_admin_shop_openinghour',
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

        $form = $this->getForm('shop_admin_session_opening-hour_edit', $openingHour);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The opening hour was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'shop_admin_shop_openinghour',
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
        $form = $this->getForm('shop_admin_session_opening-hour_schedule');
        $shiftForm = $this->getForm('shift_shift_schedule');

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

                        $data = array(
                            // OPENING HOURS
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            // ALL SHIFTS
                            'name'                  => '',
                            'description'           => '',
                            'manager'               => false,
                            'unit'                  => 2,
                            'event'                 => '',
                            'location'              => 1,
                            'nb_responsibles'       => 0,
                            'nb_volunteers'         => 4,
                            'nb_volunteers_min'     => 2,
                            'reward'                => 2,
                            'handled_on_event'      => false,
                            'ticket_needed'         => false,
                            'points'                => 0,
                        );

                        // OPENING HOURS
                        $this->getEntityManager()->persist(
                            $form->getHydrator()->hydrate($data)
                        );

                        // SHIFTS
                        if ($formData['shift_' . $split[2]]) {
                            // Broodjes smeren
                            $data['name'] = 'Broodjes smeren';
                            $data['description'] = 'Kom broodjes smeren! (gratis degustaties inbegrepen)';
                            $data['start_date'] = $split[2] . ' 10:30';
                            $data['end_date'] = $split[2] . ' 12:30';
                            $this->getEntityManager()->persist(
                                $shiftForm->getHydrator()->hydrate($data)
                            );
                            // Broodjes verkopen
                            $data['name'] = 'Broodjes verkopen';
                            $data['description'] = 'Kom broodjes verkopen en geniet van een zelfgemaakt broodje en de geweldige shiftersvoorzieningen!';
                            $data['start_date'] = $split[2] . ' 12:30';
                            $data['end_date'] = $split[2] . ' 14:00';
                            $this->getEntityManager()->persist(
                                $shiftForm->getHydrator()->hydrate($data)
                            );
                            // Namiddagverkoop
                            $data['name'] = 'Namiddagverkoop';
                            $data['description'] = 'Help ons met de laatste broodjes te verkopen, maak en paar croques en geniet van een mooie namiddag.';
                            $data['start_date'] = $split[2] . ' 14:00';
                            $data['end_date'] = $split[2] . ' 16:00';
                            $data['nb_volunteers'] = 2;
                            $data['nb_volunteers_min'] = 0;
                            $this->getEntityManager()->persist(
                                $shiftForm->getHydrator()->hydrate($data)
                            );
                        }
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'This schedule was successfully added!'
                );

                $this->redirect()->toRoute(
                    'shop_admin_shop_openinghour',
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
        $openingHour = $this->getEntityById('ShopBundle\Entity\Session\OpeningHour');

        if (!($openingHour instanceof OpeningHour)) {
            $this->flashMessenger()->error(
                'Error',
                'No opening hour was found!'
            );

            $this->redirect()->toRoute(
                'shop_admin_shop_openinghour',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $openingHour;
    }

}
