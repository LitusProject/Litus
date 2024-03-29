<?php

namespace CommonBundle\Component\Util;

/**
 * A priority queue where elements with the same priority are compared
 * alphabetically.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class NamedPriorityQueue extends \SplPriorityQueue
{
    /**
     * Compares two priorities.
     *
     * @param  array $a
     * @param  array $b
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
        if (!is_array($priority)) {
            $priority = array(0, $priority);
        }

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
