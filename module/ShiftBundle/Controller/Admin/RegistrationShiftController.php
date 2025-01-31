<?php

namespace ShiftBundle\Controller\Admin;

use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use DateInterval;
use DateTime;
use Laminas\Http\Headers;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;
use ShiftBundle\Entity\RegistrationShift;

/**
 * ShiftController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RegistrationShiftController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\RegistrationShift')
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
                ->getRepository('ShiftBundle\Entity\RegistrationShift')
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
        $form = $this->getForm('shift_registrationShift_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $startDate = self::loadDate($formData['start_date']);
                $endDate = self::loadDate($formData['end_date']);
                $signoutDate = self::loadDate($formData['signout_date']);
                $finalSigninDate = self::loadDate($formData['final_signin_date']);

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
                        $shift->setSignoutDate(
                            $this->addInterval(clone $signoutDate, $interval, $j)
                        );
                        $shift->setFinalSigninDate(
                            $this->addInterval(clone $finalSigninDate, $interval, $j)
                        );

                        $this->getEntityManager()->persist($shift);
                    }

                    $startDate = $startDate->modify('+1 day');
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The registration shift was successfully created!'
                );

                $this->redirect()->toRoute(
                    'shift_admin_registration_shift',
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

    public function editAction()
    {
        $shift = $this->getRegistrationShiftEntity();
        if ($shift === null) {
            return new ViewModel();
        }

        $form = $this->getForm('shift_registrationShift_edit', array('registrationShift' => $shift));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $registeredBefore = $shift->getRegistered();

            if ($formData['handled_on_event'] == '') {
                $formData['handled_on_event'] = 0;
            }

            $form->setData($formData);

            if ($form->isValid()) {
                $registeredAfter = $shift->getRegistered();

                $registeredDiff = array_diff_key($registeredBefore, $registeredAfter);

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

                $shiftString = $shift->getName() . ' from ' . $shift->getStartDate()->format('d/m/Y H:i') . ' to ' . $shift->getEndDate()->format('d/m/Y H:i');

                $mail = new Message();
                $mail->setEncoding('UTF-8')
                    ->setBody(str_replace('{{ shift }}', $shiftString, $message))
                    ->setFrom($mailAddress, $mailName)
                    ->setSubject($subject);

                $mail->addTo($mailAddress, $mailName);

                foreach ($registeredDiff as $register) {
                    $mail->addBcc($register->getPerson()->getEmail(), $register->getPerson()->getFullName());
                }

                if (getenv('APPLICATION_ENV') != 'development') {
                    $this->getMailTransport()->send($mail);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The registration shift was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'shift_admin_registration_shift',
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

    public function csvAction()
    {
        $timeslot = $this->getRegistrationShiftEntity();
        if ($timeslot === null) {
            return new ViewModel();
        }

        $file = new CsvFile();
        $heading = array('Timeslot Title', 'Author', 'Start Date', 'End Date', 'Registered Name', 'r-number', 'Phone Number', 'Email Address',);

        $results = array();

        $registered = $timeslot->getRegistered();

        foreach ($registered as $register) {
            $results[] = array($timeslot->getName(),
                $timeslot->getCreationPerson()->getFullName(),
                $timeslot->getStartDate()->format('j/m/Y'),
                $timeslot->getEndDate()->format('j/m/Y'),
                $register->getPerson()->getFullName(),
                $register->getPerson()->getUniversityIdentification(),
                $register->getPerson()->getPhoneNumber(),
                $register->getPerson()->getPersonalEmail(),
            );
        }


        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="contracts.csv"',
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

    public function deleteAction()
    {
        $this->initAjax();

        $shift = $this->getRegistrationShiftEntity();
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

        $shiftString = $shift->getName() . ' from ' . $shift->getStartDate()->format('d/m/Y H:i') . ' to ' . $shift->getEndDate()->format('d/m/Y H:i');

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody(str_replace('{{ shift }}', $shiftString, $message))
            ->setFrom($mailAddress, $mailName)
            ->setSubject($subject);

        $mail->addTo($mailAddress, $mailName);

        foreach ($shift->getRegistered() as $register) {
            $mail->addBcc($register->getPerson()->getEmail(), $register->getPerson()->getFullName());
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
                    ->getRepository('ShiftBundle\Entity\RegistrationShift')
                    ->findAllActiveByNameQuery($this->getParam('string'));
        }
    }

    /**
     * @return RegistrationShift|null
     */
    private function getRegistrationShiftEntity()
    {
        $shift = $this->getEntityById('ShiftBundle\Entity\RegistrationShift');

        if (!($shift instanceof RegistrationShift)) {
            $this->flashMessenger()->error(
                'Error',
                'No registration shift was found!'
            );

            $this->redirect()->toRoute(
                'shift_admin_registration_shift',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $shift;
    }

    /**
     * @param  string $date
     * @return DateTime|null
     */
    private static function loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date) ?: null;
    }
}
