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

foreach (new DirectoryIterator('public/_gallery/albums') as $fileInfo) {
    if($fileInfo->isDot())
        continue;

    if ($fileInfo->isDir())
        fixAlbum($fileInfo->getPathname());
    $files[] = $fileInfo->getFilename();
}

function fixAlbum($path)
{
    foreach (new DirectoryIterator($path) as $fileInfo) {
        if($fileInfo->isDot() || $fileInfo->isDir())
            continue;

        if (strpos($fileInfo->getFilename(), '.jpg') === false)
            continue;

        list($width, $height, $type, $attr) = getimagesize(dirname($fileInfo->getPathname()) . '/thumbs/' . $fileInfo->getFilename());
        if ($width == 150 && $height == 150)
            continue;

        $image = new Imagick($fileInfo->getPathname());
        $image->cropThumbnailImage(150, 150);
        $image->writeImage(dirname($fileInfo->getPathname()) . '/thumbs/' . $fileInfo->getFilename());
    }
}
