<?php

updateConfigKey($connection, 'common.profile_path', '/_common/profile');
removeAclAction($connection, 'common_account', 'photo');
removeAclAction($connection, 'br_corporate_cv', 'cvPhoto');

foreach (new DirectoryIterator('data/common/profile') as $fileInfo) {
    if($fileInfo->isDot() || $fileInfo->getFilename() == 'README.md')
        continue;

    rename('data/common/profile/' . $fileInfo->getFilename(), 'public/_common/profile/' . $fileInfo->getFilename());
}

rrmdir('data/common/');

function rrmdir($dir)
{
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
}