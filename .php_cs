<?php

$finder = Litus\CodeStyle\Finder\Finder::create()
    ->exclude('data')
    ->exclude('GalleryBundle/Resources/assets/plupload/examples')
    ->in(__DIR__);

return Litus\CodeStyle\Config\Config::create()
    ->setLicense(__DIR__ . '/.license_header')
    ->finder($finder);
