<?php

namespace CommonBundle\Component\Module;

/**
 * This class provides a static method to create module configurations.
 *
 * Configuration is split up into four files:
 * - module.config.php
 *       The main file. Runs \CommonBundle\Component\Module\Config::create
 *       (not in BootstrapBundle)
 * - router.config.php
 *       Optional. Returns an array:
 *       return array(
 *           'routes' => array(
 *               // the routes
 *           ),
 *           'controllers' => array(
 *               // a mapping of controller name to class
 *           ),
 *       );
 * - assetic.config.php
 *       Optional. Returns an array:
 *       return array(
 *           'controllers' => array(
 *               // defines which resources are to be loaded based on the controller
 *           ),
 *           'routes' => array(
 *               // defines which resources are to be loaded based on the route
 *           ),
 *           'collections' => array(
 *               // names collections of assets
 *           ),
 *       );
 * - admin.config.php
 *       Optional. Returns an array:
 *       return array(
 *           'general'     => array(
 *               // Optional. Defines general menus to show in the admin
 *               <group_name> => array(
 *                   <controller> => array(
 *                       'action' => 'manage', // optional, the action
 *                       'title'  => ,         // required, the title
 *                       'help'   => null,     // optional, help text
 *                   ),
 *                   // or using the shorthand if action is 'manage':
 *                   <controller> => <title>,
 *               ),
 *           ),
 *           'submenus'    => array(
 *               // Optional. Defines submenus to show in the admin
 *               <name> => array(
 *                   'subtitle'    => array(
 *                       // Optional. Defines words to add to the subtitle
 *                   ),
 *                   'items'       => array(
 *                       // Optional. Defines items to add to the submenu
 *                       <controller> => array(
 *                           'action' => 'manage', // optional, the action
 *                           'title'  => ,         // required, the title
 *                           'help'   => null,     // optional, help text
 *                       ),
 *                       // shorthand for controller with action 'manage':
 *                       <controller> => <title>,
 *                   ),
 *                   'controllers' => array(
 *                       // Optional. Defines extra controllers that make this submenu "active"
 *                   ),
 *               ),
 *           ),
 *       );
 *
 * This class creates a standard configuration based on a couple of settings the module.config.php
 * file provides:
 *       public static function create(array $settings, array $override) { ... }
 * The parameters are (default values are given):
 *       $settings = array(
 *           'namespace'         =>         , // required, the namespace of the bundle to configure
 *           'directory'         =>         , // required, the directory of the module.config.php file
 *           'has_entities'      => true    , // optional, whether or not the bundle has entities
 *           'translation_files' => array() , // optional, files in ../translations for i18n
 *           'has_views'         => true    , // optional, whether the bundle has Twig views
 *           'has_layouts'       => false   , // optional, whether the bundle has Twig layouts
 *       );
 *       $override = array(
 *           // elements in this array are merged into the result of the method before it is returned
 *           // using array_merge_recursive.
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
     * @param  string $directory the directory containing the file
     * @param  string $file      the file to load
     * @return mixed
     */
    private static function load($directory, $file)
    {
        $file = $directory . '/' . $file;
        if (file_exists($file)) {
            return include $file;
        } else {
            return array();
        }
    }

    private static function createTranslationConfig(array $settings)
    {
        if (!array_key_exists('translation_files', $settings)) {
            return array();
        }

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

    private static function createDoctrineConfig(array $settings)
    {
        $doctrine = array();
        $directory = $settings['directory'];
        $namespace = $settings['namespace'];

        // include entities by default
        if (!array_key_exists('has_entities', $settings) || $settings['has_entities']) {
            $doctrine['orm_default'] = array(
                'drivers' => array(
                    $namespace . '\Entity' => 'orm_annotation_driver',
                ),
            );
            $doctrine['orm_annotation_driver'] = array(
                'paths' => array(
                    $namespace => $directory . '/../../Entity',
                ),
            );
        }

        return $doctrine;
    }

    private static function createViewManagerConfig(array $settings)
    {
        // include view by default
        $hasView = !array_key_exists('has_views', $settings) || $settings['has_views'];

        // don't include layout by default
        $hasLayout = array_key_exists('has_layouts', $settings) && $settings['has_layouts'];

        if (!$hasView && !$hasLayout) {
            return array();
        }

        $directory = $settings['directory'];
        $namespace = $settings['namespace'];
        $bundleName = str_replace('bundle', '', strtolower($namespace));

        $templatePathStack = array();
        if ($hasLayout) {
            $templatePathStack[$bundleName . '_layout'] = $directory . '/../layouts';
        }
        if ($hasView) {
            $templatePathStack[$bundleName . '_view'] = $directory . '/../views';
        }

        return array(
            'template_path_stack' => $templatePathStack,
        );
    }

    private static function createAsseticConfig(array $settings)
    {
        $directory = $settings['directory'];
        $asseticConfig = self::load($directory, 'assetic.config.php');

        $result = array();

        if (array_key_exists('routes', $asseticConfig)) {
            $result['routes'] = $asseticConfig['routes'];
        }
        if (array_key_exists('controllers', $asseticConfig)) {
            $result['controllers'] = $asseticConfig['controllers'];
        }
        if (array_key_exists('collections', $asseticConfig)) {
            $namespace = $settings['namespace'];

            $result['modules'] = array(
                strtolower($namespace) => array(
                    'root_path'   => $directory . '/../assets',
                    'collections' => $asseticConfig['collections'],
                ),
            );
        }

        return $result;
    }

    private static function createInstallConfig(array $settings)
    {
        $directory = $settings['directory'] . '/install';
        $namespace = $settings['namespace'];

        // returning an empty array here will ensure an error is thrown in the
        // installcontroller.
        if (!file_exists($directory)) {
            return array();
        }

        $result = array();

        foreach (array('configuration', 'acl', 'roles') as $name) {
            $file = $directory . '/' . $name . '.config.php';
            if (file_exists($file)) {
                $result[$name] = $file;
            }
        }

        return array(
            $namespace => $result,
        );
    }

    /**
     *
     * @return array
     */
    public static function create(array $settings, array $override = array())
    {
        $directory = $settings['directory'];
        $routerConfig = self::load($directory, 'router.config.php');

        return array_merge_recursive(
            array(
                'router'                => array(
                    'routes' => array_key_exists('routes', $routerConfig) ? $routerConfig['routes'] : array(),
                ),
                'controllers'           => array(
                    'invokables' => array_key_exists('controllers', $routerConfig) ? $routerConfig['controllers'] : array(),
                ),

                'translator'            => array(
                    'translation_file_patterns' => self::createTranslationConfig($settings),
                ),

                'doctrine'              => array(
                    'driver' => self::createDoctrineConfig($settings),
                ),

                'assetic_configuration' => self::createAsseticConfig($settings),

                'view_manager'          => self::createViewManagerConfig($settings),

                'litus'                 => array(
                    'admin'   => self::load($directory, 'admin.config.php'),
                    'install' => self::createInstallConfig($settings),
                    'console' => self::load($directory, 'console.config.php'),
                ),
            ),
            $override
        );
    }
}
