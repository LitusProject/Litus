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
	'controllers'  => array(
		'admin_auth'      => array(
		    '@admin_auth_js',
		),
		'admin_dashboard' => array(
		    '@admin_base_css',
		    '@admin_base_js',
		),
		'admin_role'      => array(
		    '@admin_base_css',
		    '@admin_base_js',
		),
		'admin_user'      => array(
		    '@admin_base_css',
		    '@admin_base_js',
		),
		'admin_user' => array(
		    '@admin_base_css',
		    '@admin_base_js',
		),
	),
	'routes' => array(),
);
