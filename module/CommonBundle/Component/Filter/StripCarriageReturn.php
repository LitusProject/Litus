<?php

namespace CommonBundle\Component\Filter;

/**
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class StripCarriageReturn extends \Laminas\Filter\AbstractFilter
{
    /**
     * Defined by Laminas\Filter\FilterInterface
     *
     * Returns $value without carriage return control characters
     *
     * @param  string|array $value
     * @return string|array
     */
    public function filter($value)
    {
        if (!is_scalar($value) && !is_array($value)) {
            return $value;
        }

        return str_replace("\r", '', $value);
    }
}
