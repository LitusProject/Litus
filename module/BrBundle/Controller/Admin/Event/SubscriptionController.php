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
use BrBundle\Entity\Event\Subscription as SubscriptionEntity;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use Laminas\Http\Headers;
use Laminas\Mail\Message;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;
use Laminas\View\Model\ViewModel;

/**
 * SubscriptionController
 *
 * Controller for the subscribers attending the events organised by VTK Corporate Relations itself.
 *
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 */
class SubscriptionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function overviewAction()
    {
        $eventObject = $this->getEventEntity();
        if ($eventObject === null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event\Subscription')
                ->findAllByEventQuery($eventObject),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'event'             => $eventObject,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $subscription = $this->getSubscriptionEntity();
        if ($subscription === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($subscription);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function addAction()
    {
        $eventObject = $this->getEventEntity();

        $form = $this->getForm('br_event_subscription_add', array('event' => $eventObject));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            
            if ($form->isValid()) {
                $subscription = $form->hydrateObject();
                $subscription->setEvent($eventObject);
                $this->getEntityManager()->persist(
                    $subscription
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The Subscription was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_event_subscription',
                    array(
                        'action' => 'overview',
                        'event'  => $eventObject->getId(),
                    )
                );

                return new ViewModel(
                    array(
                        'event' => $eventObject,
                    )
                );
            }
        }
        // TODO: Mailing should maybe only be done automatically with user subscription and not admin subscription

        return new ViewModel(
            array(
                'form'  => $form,
                'event' => $eventObject,
            )
        );
    }

    public function editAction()
    {
        $subscription = $this->getSubscriptionEntity();
        if ($subscription === null) {
            return new ViewModel();
        }
        $eventObject = $this->getEventEntity();


        $form = $this->getForm('br_admin_event_subscription_edit', array('event' => $eventObject, 'subscription' => $subscription));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The Subscription was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_event_subscription',
                    array(
                        'action' => 'overview',
                        'event'  => $eventObject->getId(),
                    )
                );

                return new ViewModel(
                    array(
                        'event' => $eventObject,
                    )
                );
            }
        }
        return new ViewModel(
            array(
                'form'  => $form,
                'event' => $eventObject,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();
        $event = $this->getEventEntity();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');
        

        $subscriptions = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\Subscription')
            ->findAllByEventAndNameSearchQuery($event, $this->getParam('string'))
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($subscriptions as $subscription) {
            $item = (object) array();
            $item->id = $subscription->getId();
            $item->name = $subscription->getFirstName().' '.$subscription->getLastName();
            $item->university = $subscription->getUniversityString();
            $item->study = $subscription->getStudyString();
            $item->reception = $subscription->isAtNetworkReception();
            $item->qr = $subscription->getQrCode();
            $result[] = $item;
        }


        return new ViewModel(
            array(
                'event'  => $event,
                'result' => $result,
            )
        );
    }

    public function mailAction()
    {
        $subscription = $this->getSubscriptionEntity();
        if ($subscription === null) {
            return new ViewModel();
        }
        $eventObject = $this->getEventEntity();

        $this->sendMail($eventObject, $subscription);

        $this->redirect()->toRoute(
            'br_admin_event_subscription',
            array(
                'action' => 'overview',
                'event'  => $eventObject->getId(),
            )
        );

        return new ViewModel();
    }

    public function reminderAction()
    {
        $event = $this->getEventEntity();

        $subscriptions = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\Subscription')
            ->findAllByEventQuery($event)
            ->getResult();

        foreach ($subscriptions as $subscription) {
            $this->sendReminder($event, $subscription);
        }

        $this->redirect()->toRoute(
            'br_admin_event_subscription',
            array(
                'action' => 'overview',
                'event'  => $event->getId(),
            )
        );

        return new ViewModel();
    }

    public function csvAction()
    {
        $file = new CsvFile();
        $heading = array(
            'first_name',
            'last_name',
            'email',
            'university',
            'study',
            'other_study',
            'specialization',
            'year_of_study',
            'food',
//            'network_reception',
        );
        $results = array();

        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $subscriptions = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\Subscription')
            ->findAllByEvent($event);

        foreach ($subscriptions as $subscription) {
            if ($subscription instanceof SubscriptionEntity) {
                $results[] = array(
                    $subscription->getFirstName(),
                    $subscription->getLastName(),
                    $subscription->getEmail(),
                    $subscription->getUniversityString(),
                    $subscription->getStudyString(),
                    $subscription->getSpecialization(),
                    SubscriptionEntity::POSSIBLE_STUDY_YEARS[$subscription->getStudyYear()],
                    ($event->getFood() ? $subscription->getFoodString() : '/'),
//                $subscription->isAtNetworkReception() ? 'true' : '',
                );
            }
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="subscriptions_' . $event->getTitle() . '.csv"',
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

    public function qrAction()
    {
        $subscription = $this->getSubscriptionEntity();
        if ($subscription === null) {
            return new ViewModel();
        }

        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $this->redirect()->toRoute(
            'br_career_event',
            array(
                'action' => 'qr',
                'id'     => $event->getId(),
                'code'   => $subscription->getQrCode(),
            ),
        );
    }

    private function sendMail(Event $event, SubscriptionEntity $subscription)
    {
        
        $entityManager = $this->getEntityManager();
        // $language Language is set to english when sent from admin
        $language = $entityManager->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');
        

        $mailData = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.subscription_mail_data')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = str_replace('{{event}}', $event->getTitle(), $mailData[$language->getAbbrev()]['subject']);

        $mailAddress = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.subscription_mail');

        $mailName = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.subscription_mail_name');
        
        $url = $this->url()
            ->fromRoute(
                'br_career_event',
                array('action' => 'qr',
                    'id'       => $event->getId(),
                    'code'     => $subscription->getQrCode(),
                ),
                array('force_canonical' => true)
            );

        $url = str_replace('leia.', '', $url);

        $qrSource = str_replace(
            '{{encodedUrl}}',
            urlencode($url),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.google_qr_api')
        );

        $message = str_replace('{{event}}', $event->getTitle(), $message);
        $message = str_replace('{{eventDate}}', $event->getStartDate()->format('d/m/Y'), $message);
        $message = str_replace('{{qrSource}}', $qrSource, $message);
        $message = str_replace('{{qrLink}}', $url, $message);
        $message = str_replace('{{brMail}}', $mailAddress, $message);

        $part = new Part($message);

        $part->type = Mime::TYPE_HTML;
        $part->charset = 'utf-8';
        $newMessage = new \Laminas\Mime\Message();
        $newMessage->addPart($part);

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody($newMessage)
            ->setFrom($mailAddress, $mailName)
            ->addTo($subscription->getEmail(), $subscription->getFirstName().' '.$subscription->getLastName())
            ->setSubject($subject);

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }

    private function sendReminder(Event $event, SubscriptionEntity $subscription)
    {

        $entityManager = $this->getEntityManager();
        // $language Language is set to english when sent from admin
        $language = $entityManager->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');


        $mailData = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.subscription_reminder_data')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = str_replace('{{event}}', $event->getTitle(), $mailData[$language->getAbbrev()]['subject']);

        $mailAddress = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.subscription_mail');

        $mailName = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.subscription_mail_name');

        $url = $this->url()
            ->fromRoute(
                'br_career_event',
                array('action' => 'qr',
                    'id'       => $event->getId(),
                    'code'     => $subscription->getQrCode(),
                ),
                array('force_canonical' => true)
            );

        $url = str_replace('leia.', '', $url);
        $url = str_replace('/en/', '/nl/', $url);

        $qrSource = str_replace(
            '{{encodedUrl}}',
            urlencode($url),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.google_qr_api')
        );

        $message = str_replace('{{event}}', $event->getTitle(), $message);
        $message = str_replace('{{eventDate}}', $event->getStartDate()->format('d/m/Y'), $message);
        $message = str_replace('{{qrSource}}', $qrSource, $message);
        $message = str_replace('{{qrLink}}', $url, $message);
        $message = str_replace('{{brMail}}', $mailAddress, $message);

        $part = new Part($message);

        $part->type = Mime::TYPE_HTML;
        $part->charset = 'utf-8';
        $newMessage = new \Laminas\Mime\Message();
        $newMessage->addPart($part);

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody($newMessage)
            ->setFrom($mailAddress, $mailName)
            ->addTo($subscription->getEmail(), $subscription->getFirstName().' '.$subscription->getLastName())
            ->setSubject($subject);

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
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

            return;
        }

        return $event;
    }

    /**
     * @return SubscriptionEntity|null
     */
    private function getSubscriptionEntity()
    {
        $subscription = $this->getEntityById('BrBundle\Entity\Event\Subscription');

        if (!($subscription instanceof SubscriptionEntity)) {
            $this->flashMessenger()->error(
                'Error',
                'No company mapping was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_event_company',
                array(
                    'action' => 'manage',
                    'event'  => $this->getEventEntity()->getId(),
                )
            );

            return;
        }

        return $subscription;
    }
}
