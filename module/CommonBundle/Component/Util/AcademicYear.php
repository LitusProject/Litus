<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CommonBundle\Component\Util;

use DateTime,
    DateInterval;

/**
 * Utility class containing methods used to retrieve the academic year
 * or promotion year for a given DateTime object.
 *
 * Note:
 * DateTime::modify(...) modifies the object, and returns the modified version.
 * To ensure that the given variable does not change, it should always be cloned.
 * DateTime does not implement a clone() method, but the following is the same:
 *
 *     $clone = new DateTime($date->format(DateTime::ISO8601))
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class AcademicYear
{
    /**
     * Returns the Academic year in yyyy-zzzz notation (i.e. 2009-2010, 2011-2012)
     * for the given date. If no date is given, the current date is used.
     *
     * @static
     * @param \DateTime $date If null, the current date is used
     * @return string The academic year in yyyy-zzzz notation
     */
    public static function getAcademicYear(DateTime $date = null)
    {
        if ($date === null)
            $date = new DateTime('now');

        $startOfAcademicYear = AcademicYear::getStartOfAcademicYear($date);

        if ($date > $startOfAcademicYear) {
            $startYear = $startOfAcademicYear->format('Y');
            $endYear = $startOfAcademicYear->modify('+1 year')->format('Y');
        } else {
            $endYear = $startOfAcademicYear->format('Y');
            $startYear = $startOfAcademicYear->modify('-1 year')->format('Y');
        }

        return $startYear . '-' . $endYear;
    }

    /**
     * Returns the Academic year in yyzz notation (i.e. 0910, 1112) for the given date.
     * If no date is given, the current date is used.
     *
     * @static
     * @param \DateTime $date If null, the current date is used
     * @return string The academic year in yyzz format
     */
    public static function getShortAcademicYear(DateTime $date = null)
    {
        if ($date === null)
            $date = new DateTime('now');

        $startOfAcademicYear = AcademicYear::getStartOfAcademicYear($date);

        if ($date > $startOfAcademicYear) {
            $startYear = $startOfAcademicYear->format('y');
            $endYear = $startOfAcademicYear->modify('+1 year')->format('y');
        } else {
            $endYear = $startOfAcademicYear->format('y');
            $startYear = $startOfAcademicYear->modify('-1 year')->format('y');
        }

        return $startYear . $endYear;
    }

    /**
     * Returns the start of the academic year. Only the date is returned, any time should be ignored.
     *
     * @static
     * @param \DateTime|null $date the date, if null, the current date is used.
     * @param int $delta the start of the academic year is modified by -delta days, defaults to 0.
     * @return \DateTime the start of the academic year
     */
    public static function getStartOfAcademicYear(DateTime $date = null, $delta = 0)
    {
        if ($date === null) {
            $date = new DateTime('now');
            $currentDate = new DateTime('now');
        } else {
            $currentDate = new DateTime(
                $date->format(DateTime::ISO8601)
            );
        }

        if (($delta === null) || !is_numeric($delta))
            $delta = 0;

        do {
            $christmas = new DateTime(
                ($currentDate->format('y')) . '-12-25'
            );

            $weekDay = $christmas->format('N');

            // Saturday or Sunday
            if ($weekDay > 5) {
                $christmas->modify('-' . ($weekDay - 1) . ' days');
                $christmas->modify('+1 week');
            } else {
                $christmas->modify('-' . ($weekDay - 1) . ' days');
            }

            // One semester is 13 weeks long
            $christmas->sub(new DateInterval(
                'P' . (13 * 7) . 'D'
            ));

            $currentDate = clone $date;
            $currentDate->sub(
                new DateInterval('P1Y')
            );
        } while($christmas > $date);

        $christmas->sub(new DateInterval(
            'P' . $delta . 'D'
        ));

        return $christmas;
    }

    /**
     * Returns the end of the academic year. Only the date is returned, any time should be ignored.
     *
     * @static
     * @param \DateTime|null $date the date, if null, the current date is used.
     * @return \DateTime the end of the academic year
     */
    public static function getEndOfAcademicYear(DateTime $date = null)
    {
        $date = self::getStartOfAcademicYear($date);
        $date->add(
            new DateInterval('P1Y3M')
        );
        return self::getStartOfAcademicYear($date);
    }

    /**
     * Returns the start of the Academic year for the given Academic year.
     *
     * @static
     * @param string $academicYear The academic year in yyzz format
     * @return \DateTime
     */
    public static function getDateTime($academicYear)
    {
        $startYear = new DateTime(
            (substr($academicYear, 0, (strpos($academicYear, '-') === false ? 2 : 4))) . '-12-1'
        );
        return self::getStartOfAcademicYear($startYear);
    }

    /**
     * Returns the promotion year for the given date. This is the last year in the academic year.
     *
     * @static
     * @param \DateTime|null $date if null, the current date is used.
     * @return string the promotion year, in yyyy format (i.e. 2010, 2012).
     */
    public static function getGraduationYear(DateTime $date = null)
    {
        if ($date === null) {
            $date = new DateTime('now');
        } else {
            $date = new DateTime(
                $date->format(DateTime::ISO8601)
            );
        }

        $startAcademicYear = self::getStartOfAcademicYear($date);

        if ($date < $startAcademicYear)
            return $date->format('Y');
        else
            return $date->modify('+1 year')->format('Y');
    }
}
