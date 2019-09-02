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
use ShiftBundle\Entity\Shift;
use Zend\Http\Headers;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;

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
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
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
                'paginator'         => $paginator,
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
                'form' => $form,
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
}
