<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShiftBundle\Controller;

use CommonBundle\Entity\Users\People\Academic,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    ShiftBundle\Form\Shift\Search\Event as EventSearchForm,
    ShiftBundle\Form\Shift\Search\Unit as UnitSearchForm,
    CudiBundle\Entity\Sales\Booking,
    Zend\View\Model\ViewModel;

/**
 * ShiftController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ShiftController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function overviewAction()
    {
        $eventSearchForm = new EventSearchForm($this->getEntityManager(), $this->getLanguage());
        $unitSearchForm = new UnitSearchForm($this->getEntityManager());

        $searchResults = null;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData['event'])) {
                $eventSearchForm->setData($formData);

                if ($eventSearchForm->isValid() && '' != $formData['event']) {
                    $event = $this->getEntityManager()
                        ->getRepository('CalendarBundle\Entity\Nodes\Event')
                        ->findOneById($formData['event']);

                    $searchResults = $this->getEntityManager()
                        ->getRepository('ShiftBundle\Entity\Shift')
                        ->findAllActiveByEvent($event);
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'The given search query was invalid!'
                        )
                    );
                }
            }

            if (isset($formData['unit'])) {
                $unitSearchForm->setData($formData);

                if ($unitSearchForm->isValid() && '' != $formData['unit']) {
                    $unit = $this->getEntityManager()
                        ->getRepository('ShiftBundle\Entity\Unit')
                        ->findOneById($formData['unit']);

                    $searchResults = $this->getEntityManager()
                        ->getRepository('ShiftBundle\Entity\Shift')
                        ->findAllActiveByUnit($unit);
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'The given search query was invalid!'
                        )
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'eventSearchForm' => $eventSearchForm,
                'unitSearchForm' => $unitSearchForm,
                'searchResults' => $searchResults
            )
        );
    }

    public function cancelAction()
    {
        //$this->initAjax();

        if (!($booking = $this->_getBooking()))
            return new ViewModel();

        if (!($booking->getArticle()->isUnbookable())) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'The given booking cannot be cancelled!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_booking',
                array(
                    'action' => 'view',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return new ViewModel();
        }

        $booking->setStatus('canceled');
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function signupAction()
    {
        
    }

    private function _getBooking()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the booking!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_booking',
                array(
                    'action' => 'view',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        $booking = $this->getEntityManager()
        ->getRepository('CudiBundle\Entity\Sales\Booking')
        ->findOneById($this->getParam('id'));

        if (null === $booking) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No booking with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_booking',
                array(
                    'action' => 'view',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $booking;
    }
}