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

namespace CalendarBundle\Hydrator\Node;

use CalendarBundle\Entity\Node\Event as EventEntity,
    CommonBundle\Component\Hydrator\Exception\InvalidDateException,
    CommonBundle\Component\Hydrator\Exception\InvalidObjectException,
    DateTime;

/**
 * This hydrator hydrates/extracts event data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Event extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $std_keys = array();

    protected function doHydrate(array $data, $object = null)
    {
        // EventEntity requires the Person that created it, so
        // we cannot create an object here.
        if (null === $object)
            throw new InvalidObjectException();

        $startDate = self::_loadDate($data['start_date']);
        $endDate = self::_loadDate($data['end_date']);

        if (null === $startDate || null === $endDate)
            throw new InvalidDateException();

        $object->setStartDate($startDate);
        $object->setEndDate($endDate);

        return $this->stdHydrate($data, $object, self::$std_keys);
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$std_keys);

        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        if (null !== $object->getEndDate())
            $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');

        return $data;
    }

    /**
     * @param  string        $date
     * @return DateTime|null
     */
    private static function _loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date) ?: null;
    }
}
