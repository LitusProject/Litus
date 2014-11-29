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
 *
 * @license http://litus.cc/LICENSE
 */

namespace CalendarBundle\Component\Document\Generator;

use CalendarBundle\Entity\Node\Event,
    CommonBundle\Component\Controller\Plugin\Url,
    CommonBundle\Component\Util\File\TmpFile as TmpFile,
    CommonBundle\Entity\General\Language,
    Doctrine\ORM\EntityManager,
    Zend\Http\PhpEnvironment\Request;

/**
 * Ics
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Ics
{
    /**
     * @var EntityManager The EntityManager
     */
    private $_entityManager;

    /**
     * @var Language The language
     */
    private $_language;

    /**
     * @var string The base url
     */
    private $_serverName;

    /**
     * @var string
     */
    private $_suffix;

    /**
     * @var Url
     */
    private $_url;

    /**
     * @param TmpFile       $file
     * @param EntityManager $entityManager
     * @param Language      $language
     * @param Request       $request
     * @param Url           $url
     */
    public function __construct(TmpFile $file, EntityManager $entityManager, Language $language, Request $request, Url $url)
    {
        $this->_entityManager = $entityManager;
        $this->_language = $language;
        $this->_serverName = (('on' === $request->getServer('HTTPS', 'off')) ? 'https://' : 'http://') . $request->getServer('HTTP_HOST');
        $this->_url = $url;

        $this->_suffix = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('calendar.icalendar_uid_suffix');

        $file->appendContent($this->_getHeader());
        $file->appendContent($this->_getReservations());

        $file->appendContent('END:VCALENDAR');
    }

    /**
     * @return string
     */
    private function _getHeader()
    {
        $result = 'BEGIN:VCALENDAR' . PHP_EOL;
        $result .= 'VERSION:2.0' . PHP_EOL;
        $result .= 'X-WR-CALNAME:' . $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_short_name') . ' Calendar' . PHP_EOL;
        $result .= 'PRODID:-//lituscal//NONSGML v1.0//EN' . PHP_EOL;
        $result .= 'CALSCALE:GREGORIAN' . PHP_EOL;
        $result .= 'METHOD:PUBLISH' . PHP_EOL;
        $result .= 'X-WR-TIMEZONE:Europe/Brussels' . PHP_EOL;
        $result .= 'BEGIN:VTIMEZONE' . PHP_EOL;
        $result .= 'TZID:Europe/Brussels' . PHP_EOL;
        $result .= 'X-LIC-LOCATION:Europe/Brussels' . PHP_EOL;
        $result .= 'BEGIN:DAYLIGHT' . PHP_EOL;
        $result .= 'TZOFFSETFROM:+0100' . PHP_EOL;
        $result .= 'TZOFFSETTO:+0200' . PHP_EOL;
        $result .= 'TZNAME:CEST' . PHP_EOL;
        $result .= 'DTSTART:19700329T020000' . PHP_EOL;
        $result .= 'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU' . PHP_EOL;
        $result .= 'END:DAYLIGHT' . PHP_EOL;
        $result .= 'BEGIN:STANDARD' . PHP_EOL;
        $result .= 'TZOFFSETFROM:+0200' . PHP_EOL;
        $result .= 'TZOFFSETTO:+0100' . PHP_EOL;
        $result .= 'TZNAME:CET' . PHP_EOL;
        $result .= 'DTSTART:19701025T030000' . PHP_EOL;
        $result .= 'RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU' . PHP_EOL;
        $result .= 'END:STANDARD' . PHP_EOL;
        $result .= 'END:VTIMEZONE' . PHP_EOL;

        return $result;
    }

    private function _getReservations()
    {
        $result = '';
        $events = $this->_entityManager
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllActive(0);

        foreach ($events as $event) {
            $result .= $this->_getEvent($event);
        }

        return $result;
    }

    /**
     * @param  Event  $event
     * @return string
     */
    private function _getEvent(Event $event)
    {
        $result = 'BEGIN:VEVENT' . PHP_EOL;
        $result .= 'SUMMARY:' . $event->getTitle($this->_language) . PHP_EOL;
        $result .= 'DTSTART:' . $event->getStartDate()->format('Ymd\THis') . PHP_EOL;
        if (null !== $event->getEndDate()) {
            $result .= 'DTEND:' . $event->getEndDate()->format('Ymd\THis') . PHP_EOL;
        }
        $result .= 'TRANSP:OPAQUE' . PHP_EOL;
        $result .= 'LOCATION:' . $event->getLocation($this->_language) . PHP_EOL;
        $result .= 'URL:' . $this->_serverName . $this->_url->fromRoute(
                'calendar',
                array(
                    'action' => 'view',
                    'name' => $event->getName(),
                )
            ) . PHP_EOL;
        $result .= 'CLASS:PUBLIC' . PHP_EOL;
        $result .= 'UID:' . $event->getId() . '@' . $this->_suffix . PHP_EOL;
        $result .= 'END:VEVENT' . PHP_EOL;

        return $result;
    }
}
