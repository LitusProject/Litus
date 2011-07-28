<?php

namespace Litus\Util;

use \DateTime;
use \DateInterval;

/**
 * Utility class containing methods used to retrieve the academic year or promotion year for a given DateTime object.
 *
 * @author Bram Gotink
 */
class AcademicYear
{
    /**
     * Note:
     * DateTime::modify(...) modifies the object, and returns the modified version.
     * To ensure that the given variable does not change, it should always be cloned.
     * DateTime does not implement a clone() method, but the following is the same:
     * $clone = new DateTime($date->format(DateTime::ISO8601))
     */


    /**
     * Returns the Academic year in yyyy-zzzz notation (i.e. 2009-2010, 2011-2012) for the given date.
     * If no date is given, the current date is used.
     *
     * @static
     * @param \DateTime|null $date if null, the current date is used
     * @return string the academic year in yyyy-zzzz notation
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
     * @param \DateTime|null $date if null, the current date is used
     * @return string the academic year in yyzz format.
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
        if ($date === null)
            $date = new DateTime('now');
        else
            $date = new DateTime($date->format(DateTime::ISO8601));
        if (($delta === null) || !is_numeric($delta))
            $delta = 0;

        $christmas = new DateTime(($date->format('y')) . '-12-25');

        $weekDay = $christmas->format('N');
        if ($weekDay > 5) { // => saturday/sunday
            $christmas->modify('-' . ($weekDay - 1) . ' days');
            $christmas->modify('+1 week');
        } else {
            $christmas->modify('-' . ($weekDay - 1) . ' days');
        }

        // 13 * 7 days = 13 weeks = length of one semester
        $dateInterval = new DateInterval('P' . (13 * 7 + $delta) . 'D');
        return $christmas->sub($dateInterval);
    }

    /**
     * Returns the promotion year fo the given date. This is the last year in the academic year.
     *
     * @static
     * @param \DateTime|null $date if null, the current date is used.
     * @return string the promotion year, in yyyy format (i.e. 2010, 2012).
     */
    public static function getGraduationYear(DateTime $date = null)
    {
        if ($date === null)
            $date = new DateTime('now');
        else
            $date = new DateTime($date->format(DateTime::ISO8601));

        $startAcademicYear = AcademicYear::getStartOfAcademicYear($date);
        if ($date < $startAcademicYear)
            return $date->format('Y');
        else
            return $date->modify('+1 year')->format('Y');
    }
}
