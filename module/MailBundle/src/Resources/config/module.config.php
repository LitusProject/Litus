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
    'di' => array(
        'instance' => array(
            'alias' => array(
            	'admin_mail'                   => 'MailBundle\Controller\Admin\MailController',
            ),
            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths'  => array(
                        'mail_views' => __DIR__ . '/../views',
                    ),
                ),
            ),
            
            'Zend\Mvc\Router\RouteStack' => array(
                'parameters' => array(
                    'routes' => array(
                        'admin_mail' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route'    => '/admin/mail[/:action[/:type/:group]]',
                                'constraints' => array(
			        				'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
			        				'group'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                ),
                                'defaults' => array(
                                    'controller' => 'admin_mail',
                                    'action'     => 'groups',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
