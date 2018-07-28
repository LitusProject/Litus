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

/**
 * This autoloading setup is really more complicated than it needs to be for most
 * applications. The added complexity is simply to reduce the time it takes for
 * new developers to be productive with a fresh skeleton. It allows autoloading
 * to be correctly configured, regardless of the installation method and keeps
 * the use of composer completely optional. This setup should work fine for
 * most users, however, feel free to configure autoloading however you'd like.
 */

// Composer autoloading
if (file_exists('vendor/autoload.php')) {
    $loader = include 'vendor/autoload.php';
}

// Support for ZF2_PATH environment variable or git submodule
if (($zf2Path = getenv('ZF2_PATH') ?: (is_dir('vendor/zendframework/zendframework/library') ? 'vendor/zendframework/zendframework/library' : false)) !== false) {
    if (isset($loader)) {
        $loader->add('Zend', $zf2Path);
    } else {
        include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
        Zend\Loader\AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true,
            ),
        ));
    }
}

if (!class_exists('Zend\Loader\AutoloaderFactory')) {
    throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
}
