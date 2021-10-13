<?php

namespace CommonBundle\Component\Validator;

/**
 * Verifies whether the given value is in a valid decimal format.
 *
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Decimal extends \Laminas\Validator\Regex
{
    /**
     * Sets validator options
     *
     * @param integer|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $args = func_get_args();
            $options = array();
            $options['max_after_decimal'] = array_shift($args);
        }

        parent::__construct('/^[0-9]+.?[0-9]{0,' . $options['max_after_decimal'] . '}$/');

        $this->setMessage(
            'Invalid decimal number'
        );
    }
}
