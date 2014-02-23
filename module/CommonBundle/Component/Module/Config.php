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

namespace CommonBundle\Component\Module;

/**
 * This class provides a static method to create module configurations.
 *
 * Configuration is split up into three files:
 * - module.config.php
 *       The main file. Runs \CommonBundle\Component\Module\Config::create
 *       (not in BootstrapBundle)
 * - router.config.php
 *       Optional. Returns an array:
 *       return array(
 *               'routes' => array(
 *                       // the routes
 *               ),
 *               'controllers' => array(
 *                       // a mapping of controller name to class
 *               ),
 *       );
 *- assetic.config.php
 *       Optional. Returns an array:
 *       return array(
 *               'controllers' => array(
 *                       // defines which resources are to be loaded based on the controller
 *               ),
 *               'routes' => array(
 *                       // defines which resources are to be loaded based on the route
 *               ),
 *               'collections' => array(
 *                       // names collections of assets
 *               ),
 *       );
 *
 * This class creates a standard configuration based on a couple of settings the module.config.php
 * file provides:
 *       public static function create(array $settings, array $override) { ... }
 * The parameters are (default values are given):
 *       $settings = array(
 *               'namespace'         =>         ,// required, the namespace of the bundle to configure
 *               'directory'         =>         ,// required, the directory of the module.config.php file
 *               'has_entities'      => true    ,// optional, whether or not the bundle has entities
 *               'has_documents'     => false   ,// optional, whether or not the bundle has documents
 *               'translation_files' => array() ,// optional, files in ../translations for i18n
 *               'has_views'         => true    ,// optional, whether the bundle has Twig views
 *               'has_layouts'       => false   ,// optional, whether the bundle has Twig layouts
 *       );
 *       $override = array(
 *               // elements in this array are merged into the result of the method before it is returned
 *               // using array_merge_recursive.
 *       );
 *
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Config
{
    /**
     * Loads the file in the directory if it exists, returns empty array otherwise.
     *
     * @param string $directory the directory containing the file
     * @param string $file the file to load
     * @return mixed
     */
    private static function _load($directory, $file)
    {
        $file = $directory . '/' . $file;
        if (file_exists($file))
            return include $file;
        else
            return array();
    }

    private static function _createTranslationConfig(array $settings)
    {
        if (!array_key_exists('translation_files', $settings))
            return array();

        $translationFiles = array();
        $directory = $settings['directory'];
        foreach ($settings['translation_files'] as $translationFile) {
            $translationFiles[] = array(
                'type'     => 'phparray',
                'base_dir' => $directory . '/../translations',
                'pattern'  => $translationFile . '.%s.php',
            );
        }

        return $translationFiles;
    }

    private static function _createDoctrineConfig(array $settings)
    {
        $doctrine = array();
        $directory = $settings['directory'];
        $namespace = $settings['namespace'];

        // include entities by default
        if (!array_key_exists('has_entities', $settings) || $settings['has_entities']) {
            $doctrine['orm_default'] = array(
                'drivers' => array(
                    $namespace . '\Entity' => 'orm_annotation_driver'
                ),
            );
            $doctrine['orm_annotation_driver'] = array(
                'paths' => array(
                    $namespace => $directory . '/../../Entity',
                ),
            );
        }

        // don't include documents by default
        if (array_key_exists('has_documents', $settings) && $settings['has_documents']) {
            $doctrine['odm_default'] = array(
                'drivers' => array(
                    $namespace . '\Document' => 'odm_annotation_driver'
                ),
            );
            $doctrine['odm_annotation_driver'] = array(
                'paths' => array(
                    $namespace => $directory . '/../../Document',
                ),
            );
        }

        return $doctrine;
    }

    private static function _createViewManagerConfig(array $settings)
    {
        // include view by default
        $hasView = !array_key_exists('has_views', $settings) || $settings['has_views'];
        // don't include layout by default
        $hasLayout = array_key_exists('has_layouts', $settings) && $settings['has_layouts'];

        if (!$hasView && !$hasLayout)
            return array();

        $directory = $settings['directory'];
        $namespace = $settings['namespace'];
        $bundleName = str_replace('bundle', '', strtolower($namespace));

        $templatePathStack = array();
        if ($hasLayout)
            $templatePathStack[$bundleName . '_layout'] = $directory . '/../layouts';
        if ($hasView)
            $templatePathStack[$bundleName . '_view'] = $directory . '/../views';

        return array(
            'template_path_stack' => $templatePathStack,
        );
    }

    private static function _createAsseticConfig(array $settings)
    {
        $directory = $settings['directory'];
        $asseticConfig = self::_load($directory, 'assetic.config.php');

        $result = array();

        if (array_key_exists('routes', $asseticConfig))
            $result['routes'] = $asseticConfig['routes'];
        if (array_key_exists('controllers', $asseticConfig))
            $result['controllers'] = $asseticConfig['controllers'];
        if (array_key_exists('collections', $asseticConfig)) {
            $namespace = $settings['namespace'];

            $result['modules'] = array(
                strtolower($namespace) => array(
                    'root_path' => $directory . '/../assets',
                    'collections' => $asseticConfig['collections'],
                ),
            );
        }

        return $result;
    }

    private static function _createInstallConfig(array $settings)
    {
        $directory = $settings['directory'] . '/install';
        $namespace = $settings['namespace'];

        // returning an empty array here will ensure an error is thrown in the
        // installcontroller.
        if (!file_exists($directory))
            return array();

        $result = array();

        foreach (array('configuration', 'acl', 'roles') as $name) {
            $file = $directory . '/' . $name . '.config.php';
            if (file_exists($file))
                $result[$name] = $file;
        }

        return array(
            $namespace => $result,
        );
    }

    /**
     *
     * @param string $namespace the namespace of the module
     * @param string $directory
     * @return array
     */
    public static function create(array $settings, array $override = array())
    {
        $directory = $settings['directory'];
        $namespace = $settings['namespace'];
        $routerConfig = self::_load($directory, 'router.config.php');

        return array_merge_recursive(array(
            'router' => array(
                'routes' => array_key_exists('routes', $routerConfig) ? $routerConfig['routes'] : array(),
            ),
            'controllers' => array(
                'invokables' => array_key_exists('controllers', $routerConfig) ? $routerConfig['controllers'] : array(),
            ),

            'translator' => array(
                'translation_file_patterns' => self::_createTranslationConfig($settings),
            ),

            'doctrine' => array(
                'driver' => self::_createDoctrineConfig($settings),
            ),

            'assetic_configuration' => self::_createAsseticConfig($settings),

            'view_manager' => self::_createViewManagerConfig($settings),

            'litus' => array(
                'install' => self::_createInstallConfig($settings),
            ),
        ), $override);
    }
}
