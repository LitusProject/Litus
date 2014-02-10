<?php

foreach (new DirectoryIterator('public/_gallery/albums') as $fileInfo) {
    if($fileInfo->isDot())
        continue;

    if ($fileInfo->isDir())
        fixAlbum($fileInfo->getPathname());
    $files[] = $fileInfo->getFilename();
}

function fixAlbum($path) {
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

