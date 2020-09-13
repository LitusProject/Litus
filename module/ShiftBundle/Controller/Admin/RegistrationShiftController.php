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

namespace ShiftBundle\Controller\Admin;

use CalendarBundle\Entity\Node\Event;
use CommonBundle\Component\Util\File\TmpFile;
use DateInterval;
use DateTime;
use ShiftBundle\Component\Document\Generator\Event\Pdf as PdfGenerator;
use ShiftBundle\Entity\RegistrationShift;
use ShiftBundle\Entity\Shift;
use Zend\Http\Headers;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;

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
                $visibleDate = self::loadDate($formData['visible_date']);
                $signoutDate = self::loadDate($formData['signout_date']);

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

                $shiftString = $shift->getName() . ' from ' . $shift->getStartDate()->format('d/m/Y h:i') . ' to ' . $shift->getEndDate()->format('d/m/Y h:i');

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

        $shiftString = $shift->getName() . ' from ' . $shift->getStartDate()->format('d/m/Y h:i') . ' to ' . $shift->getEndDate()->format('d/m/Y h:i');

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
                'shift_admin_registration_shift',
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
}
