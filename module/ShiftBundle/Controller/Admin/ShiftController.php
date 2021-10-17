<?php

namespace ShiftBundle\Controller\Admin;

use CalendarBundle\Entity\Node\Event;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use DateInterval;
use DateTime;
use Laminas\Http\Headers;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;
use ShiftBundle\Component\Document\Generator\Event\Pdf as PdfGenerator;
use ShiftBundle\Entity\Shift;

/**
 * ShiftController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ShiftController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\Shift')
                ->findAllActiveQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function eventAction()
    {
        $event = $this->getEventEntity();
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\Shift')
                ->findAllActiveByEventQuery($event),
            $this->getParam('page')
        );

        $shifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByEventQuery($event)->getResult();

        $shifters = array();
        foreach ($shifts as $shift){
            $shifters['Volunteers'] += $shift->countVolunteers();
            $shifters['Responsibles'] += $shift->countResponsibles();
            $shifters['NbVolunteers'] += $shift->getNbVolunteers();
            $shifters['NbResponsibles'] += $shift->getNbResponsibles();
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'event'     => $event,
                'shifters'  => $shifters,
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\Shift')
                ->findAllOldQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('shift_shift_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $startDate = self::loadDate($formData['start_date']);
                $endDate = self::loadDate($formData['end_date']);

                $formData = $form->getData();
                $interval = $startDate->diff($endDate);

                for ($i = 0; $i < $formData['duplicate_days']; $i++) {
                    for ($j = 0; $j < $formData['duplicate_hours']; $j++) {
                        $shift = $form->hydrateObject();

                        $shift->setStartDate(
                            $this->addInterval(clone $startDate, $interval, $j)
                        );
                        $shift->setEndDate(
                            $this->addInterval(clone $startDate, $interval, $j + 1)
                        );

                        $this->getEntityManager()->persist($shift);
                    }

                    $startDate = $startDate->modify('+1 day');
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The shift was successfully created!'
                );

                $this->redirect()->toRoute(
                    'shift_admin_shift',
                    array(
                        'action' => 'add',
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

    public function csvAction()
    {
        $form = $this->getForm('shift_shift_csv');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $fileData = $this->getRequest()->getFiles();

            $fileName = $fileData['file']['tmp_name'];

            $open = fopen($fileName, 'r');
            if ($open !== false) {
                $data = fgetcsv($open, 1000, ',');
                while ($data !== false) {
                    $shiftArray[] = $data;
                    $data = fgetcsv($open, 1000, ',');
                }
                fclose($open);
            }

            $form->setData($formData);

            if ($form->isValid()) {
                $creator = $this->getAuthentication()->getPersonObject();
                $academicYear = $this->getCurrentAcademicYear(true);

                $manager = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($formData['manager']['id']);

                $editRoles = array();
                if (isset($formData['edit_roles'])) {
                    $roleRepository = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Acl\Role');

                    foreach ($formData['edit_roles'] as $editRole) {
                        $editRoles[] = $roleRepository->findOneByName($editRole);
                    }
                }
                $unit = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Organization\Unit')
                    ->findOneById($formData['unit']);
                $location = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Location')
                    ->findOneById($formData['location']);
                $event = $formData['event'] == '' ? null : $this->getEntityManager()
                    ->getRepository('CalendarBundle\Entity\Node\Event')
                    ->findOneById($formData['event']);
                $handled = $formData['handled_on_event'];
                $ticket = $formData['ticket_needed'];

                $count = 0;
                foreach ($shiftArray as $key => $data) {
                    if ($key == '0') {
                        continue;
                    }
                    //Create each shift with standard variables
                    $shift = new Shift($creator, $academicYear);
                    $shift->setManager($manager)->setUnit($unit)->setLocation($location)->setEditRoles($editRoles)
                        ->setEvent($event)->setHandledOnEvent($handled)->setTicketNeeded($ticket);

                    //Add the custom variables for this particular shift
                    $shift->setStartDate(self::loadDateTime($data[0]))
                        ->setEndDate(self::loadDateTime($data[1]))
                        ->setNbResponsibles($data[2])->setNbVolunteers($data[3])
                        ->setName($data[4])->setDescription($data[5])->setReward($data[6])
                        ->setPoints($data[7]);

                    $this->getEntityManager()->persist($shift);
                    $count += 1;
                }
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    $count . ' shifts were successfully created!'
                );

                $this->redirect()->toRoute(
                    'shift_admin_shift',
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

    public function templateAction()
    {
        $rewards_enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.rewards_enabled');
        $points_enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.points_enabled');

        $file = new CsvFile();
        $heading = array(
            'start_date',
            'end_date',
            'nb_responsibles',
            'nb_volunteers',
            'name',
            'description',
            'reward',
            'points',
        );
        $now = new DateTime();

        $results = array();
        $results[] = array(
            $now->format('d/m/Y H:i'),
            date_add($now, new DateInterval('P1D'))->format('d/m/Y H:i'),
            0,
            0,
            'Name',
            'Description',
            'Amount of Rewarded Coins (0,1,2,3,4,6,10)',
            0,
        );


        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="shifts_template.csv"',
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
        $shift = $this->getShiftEntity();
        if ($shift === null) {
            return new ViewModel();
        }

        $form = $this->getForm('shift_shift_edit', array('shift' => $shift));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $volunteersBefore = $shift->getVolunteers();
            $responsiblesBefore = $shift->getResponsibles();

            if ($formData['handled_on_event'] == '') {
                $formData['handled_on_event'] = 0;
            }

            $form->setData($formData);

            if ($form->isValid()) {
                $volunteersAfter = $shift->getVolunteers();
                $responsiblesAfter = $shift->getResponsibles();

                $volunteerDiff = array_diff_key($volunteersBefore, $volunteersAfter);
                $responsiblesDiff = array_diff_key($responsiblesBefore, $responsiblesAfter);

                $mailAddress = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('shift.mail');

                $mailName = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('shift.mail_name');

                $language = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Language')
                    ->findOneByAbbrev('en');

                $mailData = unserialize(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('shift.subscription_deleted_mail')
                );

                $message = $mailData[$language->getAbbrev()]['content'];
                $subject = $mailData[$language->getAbbrev()]['subject'];

                $shiftString = $shift->getName() . ' from ' . $shift->getStartDate()->format('d/m/Y h:i') . ' to ' . $shift->getEndDate()->format('d/m/Y h:i');

                $mail = new Message();
                $mail->setEncoding('UTF-8')
                    ->setBody(str_replace('{{ shift }}', $shiftString, $message))
                    ->setFrom($mailAddress, $mailName)
                    ->setSubject($subject);

                $mail->addTo($mailAddress, $mailName);

                foreach ($volunteerDiff as $volunteer) {
                    $mail->addBcc($volunteer->getPerson()->getEmail(), $volunteer->getPerson()->getFullName());
                }

                foreach ($responsiblesDiff as $responsible) {
                    $mail->addBcc($responsible->getPerson()->getEmail(), $responsible->getPerson()->getFullName());
                }

                if (getenv('APPLICATION_ENV') != 'development') {
                    $this->getMailTransport()->send($mail);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The shift was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'shift_admin_shift',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'event' => $shift->getEvent() ?? null,
                'form' => $form,
                'em' => $this->getEntityManager(),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $shift = $this->getShiftEntity();
        if ($shift === null) {
            return new ViewModel();
        }

        $mailAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.mail');

        $mailName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.mail_name');

        $language = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.shift_deleted_mail')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = $mailData[$language->getAbbrev()]['subject'];

        $shiftString = $shift->getName() . ' from ' . $shift->getStartDate()->format('d/m/Y h:i') . ' to ' . $shift->getEndDate()->format('d/m/Y h:i');

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody(str_replace('{{ shift }}', $shiftString, $message))
            ->setFrom($mailAddress, $mailName)
            ->setSubject($subject);

        $mail->addTo($mailAddress, $mailName);

        foreach ($shift->getVolunteers() as $volunteer) {
            $mail->addBcc($volunteer->getPerson()->getEmail(), $volunteer->getPerson()->getFullName());
        }

        foreach ($shift->getResponsibles() as $responsible) {
            $mail->addBcc($responsible->getPerson()->getEmail(), $responsible->getPerson()->getFullName());
        }

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }

        $this->getEntityManager()->remove(
            $shift->prepareRemove()
        );
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $shifts = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($shifts as $shift) {
            $item = (object) array();
            $item->id = $shift->getId();
            $item->name = $shift->getName();
            $item->event = $shift->getEvent()->getTitle($this->getLanguage());
            $item->startDate = $shift->getStartDate()->format('d/m/Y H:i');
            $item->endDate = $shift->getEndDate()->format('d/m/Y H:i');

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function exportAction()
    {
        return new ViewModel(
            array(
                'form' => $this->getForm('shift_shift_export'),
            )
        );
    }

    public function pdfAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $file = new TmpFile();
        $document = new PdfGenerator($this->getEntityManager(), $event, $file);
        $document->generate();

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="shift_list.pdf"',
                'Content-Type'        => 'application/pdf',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    /**
     * @param  DateTime     $time
     * @param  DateInterval $interval
     * @param  integer      $duplicate
     * @return DateTime
     */
    private function addInterval(DateTime $time, $interval, $duplicate)
    {
        for ($i = 0; $i < $duplicate; $i++) {
            $time = $time->add($interval);
        }

        return clone $time;
    }

    /**
     *   @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('ShiftBundle\Entity\Shift')
                    ->findAllActiveByNameQuery($this->getParam('string'));
        }
    }

    /**
     * @return Shift|null
     */
    private function getShiftEntity()
    {
        $shift = $this->getEntityById('ShiftBundle\Entity\Shift');

        if (!($shift instanceof Shift)) {
            $this->flashMessenger()->error(
                'Error',
                'No shift was found!'
            );

            $this->redirect()->toRoute(
                'shift_admin_shift',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $shift;
    }

    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('CalendarBundle\Entity\Node\Event');

        if (!($event instanceof Event)) {
            $this->flashMessenger()->error(
                'Error',
                'No event was found!'
            );

            $this->redirect()->toRoute(
                'shift_admin_shift',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $event;
    }

    /**
     * @param  string $date
     * @return DateTime|null
     */
    private static function loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date) ?: null;
    }

    /**
     * Loads the given date and time.
     * @param  string $date
     * @return DateTime|null
     */
    protected static function loadDateTime($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date) ?: null;
    }
}
