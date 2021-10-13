<?php

namespace CommonBundle\Component\Redis;

use InvalidArgumentException;

class Uri
{
    /**
     * @param  array $components
     * @return string
     */
    public static function build($components, $format = 'redis')
    {
        switch ($format) {
            case 'redis':
                $uri = 'redis://';
                if (isset($components['password']) && $components['password'] != '') {
                    $uri .= ':' . $components['password'] . '@';
                }

                $uri .= $components['host'];

                if (isset($components['port'])) {
                    $uri .= ':' . $components['port'];
                }

                if (isset($components['database'])) {
                    $uri .= '/' . $components['database'];
                }

                return $uri;

            case 'tcp':
                $uri = 'tcp://' . $components['host'] . ':' . $components['port'];

                $parameters = array();
                if (isset($components['weight']) && $components['weight'] !== null) {
                    $parameters[] = 'weight=' . $components['weight'];
                }

                if (isset($components['timeout']) && $components['timeout'] !== null) {
                    $parameters[] = 'timeout=' . $components['timeout'];
                }

                if (isset($components['persistent_id']) && $components['persistent_id'] != '') {
                    $parameters[] = 'persistent=1';
                    $parameters[] = 'persistent_id=' . $components['persistent_id'];
                }

                if (isset($components['password']) && $components['password'] != '') {
                    $parameters[] = 'auth=' . $components['password'];
                }

                if (isset($components['database']) && $components['password'] != 0) {
                    $parameters[] = 'database=' . $components['database'];
                }

                return $uri . (count($parameters) > 0 ? '?' . implode('&', $parameters) : '');

            default:
                throw new InvalidArgumentException('Invalid format');
        }
    }
}
