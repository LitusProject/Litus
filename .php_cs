<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(
        array(
            __DIR__ . '/data/',
            __DIR__ . '/module/GalleryBundle/Resources/assets/plupload/examples/',
        )
    )
    ->notPath('init_autoloader.php');

return \Litus\CodeStyle\Config\Config::create()
    ->setLicense(__DIR__ . '/.license_header')
    ->setFinder($finder);
