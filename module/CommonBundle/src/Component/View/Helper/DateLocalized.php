<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CommonBundle\Component\View\Helper;

use DateTime,
    Zend\Date\Date as Zend_Date;

/**
 * A view helper that allows us to easily translate the date.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class DateLocalized extends \Zend\View\Helper\AbstractHelper
{    
    /**
     * @param \DateTime $date
     * @param string $format
     * 
     * @return Zend\Date\Date
     */
    public function __invoke(DateTime $date, $format)
    {
        $date = new Zend_Date($date->format('Y/m/d H:i:s'), 'y/M/d H:m:s');
        return $date->toString($format);
    }
}
