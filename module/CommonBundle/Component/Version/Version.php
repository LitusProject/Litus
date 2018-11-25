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

namespace CommonBundle\Component\Version;

/**
 * Factory to instantiate a console application.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Version
{
    /**
     * @param  integer|null $length
     * @return string
     */
    public static function getCommitHash($length = null)
    {
        if (file_exists(__DIR__ . '/../../../../COMMIT')) {
            $commitHash = trim(file_get_contents(__DIR__ . '/../../../../COMMIT'));
            if ($length !== null) {
                $commitHash = substr($commitHash, 0, $length);
            }
        } else {
            $commitHash = trim(exec('git rev-parse HEAD'));
            if ($length !== null) {
                $commitHash = substr($commitHash, 0, $length);
            }

            if (exec('git status --short') != '') {
                $commitHash .= '-dirty';
            }
        }

        return $commitHash;
    }

    /**
     * @return string
     */
    public static function getShortCommitHash()
    {
        return static::getCommitHash(8);
    }
}
