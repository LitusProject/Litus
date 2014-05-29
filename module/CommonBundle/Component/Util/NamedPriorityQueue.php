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

namespace CommonBundle\Component\Util;

/**
 * A priority queue where elements with the same priority are compared alphabetically
 *
 * @see strcmp
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class NamedPriorityQueue extends \SplPriorityQueue
{
    /**
     * Compares two priorities.
     *
     * @param  array   $a
     * @param  array   $b
     * @return integer
     */
    public function compare($a, $b)
    {
        return parent::compare($a[0], $b[0]) ?: strcmp($b[1], $a[1]);
    }

    /**
     * @param mixed        $data
     * @param string|array $priority an array with two elements: an integer priority
     *                               and a string name, the priority defaults to 0
     *                               if the argument is not an array
     */
    public function insert($data, $priority = '')
    {
        if (!is_array($priority))
            $priority = array(0, $priority);

        return parent::insert($data, $priority);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = array();
        $clone = clone $this;

        $clone->setExtractFlags(self::EXTR_DATA);

        foreach ($clone as $item) {
            $array[] = $item;
        }

        return $array;
    }
}
