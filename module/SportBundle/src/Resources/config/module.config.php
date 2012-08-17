<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'di'                    => array(
        'instance' => array(
            'alias'           => array(
                'admin_company' => 'BrBundle\Controller\Admin\CompanyController',
                'admin_section' => 'BrBundle\Controller\Admin\SectionController',
              ),
              'doctrine_config' => array(
                  'parameters' => array(
                      'entityPaths' => array(
                          'brbundle' => __DIR__ . '/../../Entity',
                      ),
                  ),
              ),
        ),
    ),
    'routes' => array(
        'admin_company' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/admin/company[/:action[/:id[/:confirm]]]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'confirm' => '[01]',
                ),
                'defaults' => array(
                    'controller' => 'admin_company',
                    'action'     => 'manage',
                ),
            ),
        ),
        'admin_section' => array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/admin/section[/:action[/:id[/:confirm]]]',
                'constraints' => array(
                    'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'id'      => '[0-9]*',
                    'confirm' => '[01]',
                ),
                'defaults' => array(
                    'controller' => 'admin_section',
                    'action'     => 'manage',
                ),
            ),
        ),
    ),
);
