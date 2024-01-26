<?php

namespace ApiBundle\Controller;

use DateInterval;
use DateTime;
use Laminas\View\Model\ViewModel;

/**
 * CommuController
 */
class CommuController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    /**
     * input: {
     *      "key": "api key",
     *      "start_date": "YYYYMMDD"
     *      "amount_of_days": "amount of days",
     * }
     */
    public function getCudiOpeningHoursAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $start_date = $this->getRequest()->getPost('start_date');
        $amount_of_days = $this->getRequest()->getPost('amount_of_days');

        $start = new DateTime($start_date);
        $end = clone $start;
        $end->setTime(0, 0);
        $end->add(new DateInterval('P' . $amount_of_days . 'D'));

        $openingHours = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour')
            ->findBetween($start, $end);

        $count = 0;
        $result = array();
        foreach ($openingHours as $openingHour) {
            $count++;
            $result[] = array(
                'start_date' => $openingHour->getStart()->format('Y-m-d H:i'),
                'end_date'   => $openingHour->getEnd()->format('Y-m-d H:i'),
            );
        }

        return new ViewModel(
            array(
                'result' => (object)array(
                    'status'        => 'success',
                    'opening_hours' => $result,
                ),
            )
        );
    }

    /**
     * input: {
     *      "key": "api key",
     *      "start_date": "YYYYMMDD",
     *      "amount_of_days": "amount of days",
     * }
     */
    public function getEventsAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $start_date = $this->getRequest()->getPost('start_date');
        $amount_of_days = $this->getRequest()->getPost('amount_of_days');

        $start = new DateTime($start_date);
        $end = clone $start;
        $end->setTime(0, 0);
        $end->add(new DateInterval('P' . $amount_of_days . 'D'));

        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllBetweenAndNotHidden($start, $end);

        $result = array();
        foreach ($events as $event) {
            $processedEvent = array();

            $languages = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findAll();
            foreach ($languages as $language) {
                $title = $event->getTitle($language);
                $location = $event->getLocation($language);
                $content = $event->getContent($language);

                $processedEvent[$language->getAbbrev()] = array('title' => $title,
                    'location'                                          => $location,
                    'content'                                           => $content,
                );
            }
            $processedEvent['start_date'] = $event->getStartDate()->format('Y-m-d');
            $processedEvent['start_hour'] = $event->getStartDate()->format('H:i');
            $processedEvent['end_date'] = $event->getEndDate()->format('Y-m-d');
            $processedEvent['end_hour'] = $event->getEndDate()->format('H:i');

            $result[] = $processedEvent;
        }

        return new ViewModel(
            array(
                'result' => (object)array(
                    'status' => 'success',
                    'events' => $result,
                ),
            )
        );
    }
}
