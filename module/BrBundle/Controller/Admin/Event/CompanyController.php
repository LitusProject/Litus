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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Controller\Admin\Event;

use BrBundle\Entity\Event;
use BrBundle\Entity\Event\CompanyMap;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

/**
 * CompanyController
 *
 * Controller for the Companies attending the events organised by VTK Corporate Relations itself.
 *
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 */

class CompanyController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $eventObject = $this->getEventEntity();
        if ($eventObject === null) {
            return new ViewModel();
        }
        $form = $this->getForm('br_admin_event_companyMap');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $company = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findOneById($formData['company']);
                //TODO: Check if metadata exists for given company and give it to the companyMap

                $objectMap = new CompanyMap($company, $eventObject);

                $this->getEntityManager()->persist($objectMap);
                $this->getEntityManager()->flush();
                $this->flashMessenger()->success(
                    'Success',
                    'The company was successfully added!'
                );

                $this->redirect()->toRoute(
                    'br_admin_event_company',
                    array(
                        'action' => 'manage',
                        'event'  => $eventObject->getId(),
                        'id'     => $objectMap->getCompany()->getId(),
                    )
                );
            }
        }
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event\CompanyMap')
                ->findAllByEventSortedByCompanyQuery($eventObject),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'event'             => $eventObject,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'form'              => $form,
                'currentYear'       => $this->getCurrentAcademicYear(),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $companyMap = $this->getCompanyMapEntity();
        if ($companyMap === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($companyMap);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function csvAction()
    {
        $file = new CsvFile();
        $heading = array('company', 'nb_representatives', 'location', 'cv-book', 'is_checked');
        $results = array();

        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $companyMaps = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\CompanyMap')
            ->findAllByEvent($event);
        
        $locations = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\Location')
            ->findAllByEvent($event);
        
        $positions = array();
        foreach ($locations as $location) {
            $positions[$location->getCompany()->getId()] = $location->getNumber();
        }

        $currentYear = $this->getCurrentAcademicYear();
        foreach ($companyMaps as $companyMap) {
            $results[] = array(
                $companyMap->getCompany()->getName(),
                $companyMap->getAttendees(),
                $positions[$companyMap->getCompany()->getId()],
                in_array($currentYear, $companyMap->getCompany()->getCvBookYears()),
                $companyMap->isChecked(),
            );
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="companies_'. $event->getTitle() . '.csv"',
                'Content-Type'        => 'text/csv',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function editAction()
    {
        $companyMap = $this->getCompanyMapEntity();
        if ($companyMap === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_admin_event_company_edit', array('companyMap' => $companyMap));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject($companyMap)
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The company metadata was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'br_admin_event_company',
                    array(
                        'action' => 'manage',
                        'event'  => $companyMap->getEvent()->getId(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'event'           => $companyMap->getEvent(),
                'eventCompanyMap' => $companyMap,
                'form'            => $form,
            )
        );
    }

    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('BrBundle\Entity\Event', 'event');

        if (!($event instanceof Event)) {
            $this->flashMessenger()->error(
                'Error',
                'No event was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_event',
                array(
                    'action' => 'manage',
                )
            );

            return null;
        }

        return $event;
    }

    /**
     * @return CompanyMap|null
     */
    private function getCompanyMapEntity()
    {
        $companyMap = $this->getEntityById('BrBundle\Entity\Event\CompanyMap');
        if (!($companyMap instanceof CompanyMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No company mapping was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_event_company',
                array(
                    'action' => 'manage',
                    'event'  => $this->getEventEntity(),
                )
            );

            return null;
        }

        return $companyMap;
    }

    /**
     * @return Event\CompanyAttendee|null
     */
    private function getAttendeeEntity()
    {
        $attendee = $this->getEntityById('BrBundle\Entity\Event\CompanyAttendee');

        if (!($attendee instanceof Event\CompanyAttendee)) {
            $this->flashMessenger()->error(
                'Error',
                'No attendee mapping was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_event_company',
                array(
                    'action' => 'manage',
                    'event'  => $this->getEventEntity(),
                )
            );

            return null;
        }

        return $attendee;
    }
}
