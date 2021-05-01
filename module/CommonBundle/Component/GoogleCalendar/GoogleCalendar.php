<?php


namespace CommonBundle\Component\GoogleCalendar;
use DateTime;
use Google\Exception;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

/**
 * Class GoogleCalendar
 * @package CommonBundle\Component\GoogleCalendar
 * @author Robin Wroblowski robin.wroblowski@gmail.com
 */
class GoogleCalendar
{
    /**
     * Returns an API client.
     * @param $entityManager
     * @return Google_Client the authorized client object
     * @throws Exception
     */
    public static function getClient($entityManager)
    {
        $client = new Google_Client();

        $credentials = './credentials.json';

        $serviceAccount = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.google_calendar_service_account');

        $client->setAuthConfig($credentials);
        $client->setApplicationName("Shifts");
        $client->setScopes(array(\Google_Service_Gmail::GMAIL_SETTINGS_BASIC));
        $client->setAccessType( 'offline' );
        $client->setSubject($serviceAccount);

        return $client;
    }

    /**
     * @param $entityManager
     * @param string $name
     * @param string $location
     * @param string $description
     * @param DateTime $start
     * @param DateTime $end
     * @param array $attendees
     * @return int
     * @throws Exception
     */
    public static function createEvent($entityManager, string $name, string $location, string $description, DateTime $start, DateTime $end, array $attendees){
        // Get the API client and construct the service object.
        $attenders = array();
        foreach ($attendees as $att){
            $attenders[] = array('email' => $att);
        }

        $client = self::getClient($entityManager);
        $service = new Google_Service_Calendar($client);

        $event = new Google_Service_Calendar_Event(array(
                'summary' => $name,
                'location' => $location,
                'description' => $description,
                'start' => array(
                    'dateTime' => $start->format(DateTime::ISO8601),
                ),
                'end' => array(
                    'dateTime' =>  $end->format(DateTime::ISO8601),
                ),
                'attendees' => $attenders,
                'guestsCanSeeOtherGuests' => false,
                'guestsCanInviteOthers' => false
            )
        );
        $calendarId = 'primary';

        $event = $service->events->insert($calendarId, $event, array('sendUpdates' => 'all'));
        return $event->getId();
    }

    /**
     * @param $entityManager
     * @param string $eventId
     * @param string $name
     * @param string $location
     * @param string $description
     * @param DateTime $start
     * @param DateTime $end
     * @return int
     * @throws Exception
     */
    public static function updateEvent($entityManager, string $eventId, string $name, string $location, string $description, DateTime $start, DateTime $end){
        // Get the API client and construct the service object.
        $client = self::getClient($entityManager);
        $service = new Google_Service_Calendar($client);
        $calendarId = 'primary';

        $event = $service->events->get($calendarId,$eventId);

        $event->setLocation($location);
        $event->setDescription($description);

        $startDate = new \Google_Service_Calendar_EventDateTime();
        $startDate->setDateTime($start->format(DateTime::ISO8601));
        $endDate = new \Google_Service_Calendar_EventDateTime();
        $endDate->setDateTime($end->format(DateTime::ISO8601));
        $event->setStart($startDate);
        $event->setEnd($endDate);

        $event = $service->events->update($calendarId, $eventId, $event, array('sendUpdates' => 'all'));
        return $event->getId();
    }

    /**
     * @param $entityManager
     * @param string $eventId
     * @param array $attendees
     * @return int
     * @throws Exception
     */
    public static function addAttendees($entityManager, string $eventId, array $attendees){
        // Get the API client and construct the service object.
        $client = self::getClient($entityManager);
        $service = new Google_Service_Calendar($client);
        $calendarId = 'primary';
        $event = $service->events->get($calendarId,$eventId);

        $attenders = array();
        foreach ($attendees as $att){
            $attenders[] = array('email' => $att);
        }
        foreach($event->getAttendees() as $att){
            $attenders[] = array('email' => $att->getEmail());
        }
        $event->setAttendees($attenders);
        $event = $service->events->update($calendarId, $eventId, $event, array('sendUpdates' => 'all'));
        return $event->getId();
    }

    /**
     * @param $entityManager
     * @param string $eventId
     * @param array $attendees
     * @return int
     * @throws Exception
     */
    public static function removeAttendees($entityManager, string $eventId, array $attendees){
        // Get the API client and construct the service object.
        $client = self::getClient($entityManager);
        $service = new Google_Service_Calendar($client);
        $calendarId = 'primary';
        $attenders = array();

        $event = $service->events->get($calendarId,$eventId);
        foreach($event->getAttendees() as $att){
            if (!in_array($att->getEmail(), $attendees)){
                $attenders[] = array('email' => $att->getEmail());
            }
        }
        $event->setAttendees($attenders);
        $event = $service->events->update($calendarId, $eventId, $event, array('sendUpdates' => 'all'));
        return $event->getId();
    }

    /**
     * @param $entityManager
     * @param string $eventId
     * @param array $attendees
     * @return int
     * @throws Exception
     */
    public static function editAttendees($entityManager, string $eventId, array $attendees){
        // Get the API client and construct the service object.
        $client = self::getClient($entityManager);
        $service = new Google_Service_Calendar($client);
        $calendarId = 'primary';

        $attenders = array();
        foreach ($attendees as $att){
            $attenders[] = array('email' => $att);
        }

        $event = $service->events->get($calendarId,$eventId);
        $event->setAttendees($attenders);
        $event = $service->events->update($calendarId, $eventId, $event, array('sendUpdates' => 'all'));
        return $event->getId();
    }

    /**
     * @param $entityManager
     * @param string $eventId
     * @return void
     * @throws Exception
     */
    public static function remove($entityManager, string $eventId){
        // Get the API client and construct the service object.
        $client = self::getClient($entityManager);
        $service = new Google_Service_Calendar($client);
        $calendarId = 'primary';

        $event = $service->events->get($calendarId,$eventId);

        if ($event->getEnd() > new DateTime()){
            $service->events->delete($calendarId, $eventId, array('sendUpdates' => 'all', 'sendNotification' => true));
        } else {
            $service->events->delete($calendarId, $eventId);
        }
        return;
    }
}
GoogleCalendar::getClient();