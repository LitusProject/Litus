<?php

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
        $commitHash = '(devel)';
        if (isset($_ENV['COMMIT'])) {
            $commitHash = $_ENV['COMMIT'];
            if ($length !== null) {
                $commitHash = substr($commitHash, 0, $length);
            }
        } elseif (file_exists(__DIR__ . '/../../../../COMMIT')) {
            $commitHash = trim(file_get_contents(__DIR__ . '/../../../../COMMIT'));
            if ($length !== null) {
                $commitHash = substr($commitHash, 0, $length);
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
