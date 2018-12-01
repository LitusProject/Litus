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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

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

                $options = array();
                if (isset($components['password']) && $components['password'] != '') {
                    $options[] = 'auth=' . $components['password'];
                }

                if (isset($components['database']) && $components['password'] != 0) {
                    $options[] = 'database=' . $components['database'];
                }

                if (isset($components['timeout']) && $components['timeout'] !== null) {
                    $options[] = 'timeout=' . $components['timeout'];
                }

                return $uri . (count($options) > 0 ? '?' . implode('&', $options) : '');

            default:
                throw new InvalidArgumentException('Invalid format');
        }
    }
}
