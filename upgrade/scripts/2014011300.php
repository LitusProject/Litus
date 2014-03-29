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

updateConfigKey($connection, 'common.profile_path', '/_common/profile');
removeAclAction($connection, 'common_account', 'photo');
removeAclAction($connection, 'br_corporate_cv', 'cvPhoto');

foreach (new DirectoryIterator('data/common/profile') as $fileInfo) {
    if($fileInfo->isDot() || $fileInfo->getFilename() == 'README.md')
        continue;

    rename('data/common/profile/' . $fileInfo->getFilename(), 'public/_common/profile/' . $fileInfo->getFilename());
}

rrmdir('data/common/');

if (!function_exists("rrmdir")) {
    function rrmdir($dir)
    {
        foreach (glob($dir . '/*') as $file) {
            if(is_dir($file))
                rrmdir($file);
            else
                unlink($file);
        }
        rmdir($dir);
    }
}
