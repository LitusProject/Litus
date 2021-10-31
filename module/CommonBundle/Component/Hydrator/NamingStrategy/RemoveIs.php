<?php

namespace CommonBundle\Component\Hydrator\NamingStrategy;

/**
 * Make method names like <i>is</i>Something() result in 'something' rather than
 * 'is_something'.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class RemoveIs extends \Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy
{
    public function extract($name, $object = null)
    {
        $extracted = parent::extract($name);

        if (strpos($extracted, 'is_') !== 0) {
            return $extracted;
        }

        return substr($extracted, 3);
    }
}
