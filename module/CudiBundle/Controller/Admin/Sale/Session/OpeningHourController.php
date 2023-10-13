<?php

namespace CudiBundle\Controller\Admin\Sale\Session;

use DateTime;
use CudiBundle\Entity\Sale\Session\OpeningHour;
use Laminas\View\Model\ViewModel;

/**
 * OpeningHourController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
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
                error_log(json_encode($form->getData()));
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

        $monday = new DateTime();                                                   // create DateTime object with current time
        $monday->setISODate($monday->format('o'), $monday->format('W') + 1);        // set object to Monday on next week

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();
                foreach ($formData as $formKey => $formValue) {
                    $split = explode("_", $formKey);
                    if ($split[0] == 'interval' && $formValue) {
                        $date = $split[2];  // for readability create extra variables, could also just plug it in $data array
                        $startHour = explode('-', $split[1])[0];
                        $endHour = explode('-', $split[1])[1];

                        $data = array();
                        $data["start"] = $date . ' ' . $startHour;
                        $data["end"] = $date . ' ' . $endHour;

                        $this->getEntityManager()->persist(
                            $form->getHydrator()->hydrate($data)
                        );
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
}
