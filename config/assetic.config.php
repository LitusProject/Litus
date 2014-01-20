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

$settings = array(
    'controllers'  => array(),
    'routes' => array(),
);

if ($handle = opendir(__DIR__ . '/assetic')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != '.' && $entry != '..' && $entry != 'README.md') {
            $moduleConfig = include __DIR__ . '/assetic/' . $entry;

            $settings['controllers'] = array_merge_recursive(
                $settings['controllers'], $moduleConfig['controllers']
            );

            $settings['routes'] = array_merge_recursive(
                $settings['routes'], $moduleConfig['routes']
            );
        }
    }
    closedir($handle);
}

return $settings;
