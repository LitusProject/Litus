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
	'controllers' => array(
		'common_install' => array(
			'@common_jquery',
			'@admin_css',
			'@admin_js',
		),
		
		'admin_academic' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		),
		'admin_auth' => array(
		    '@common_jquery',
		),
		'admin_config' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		),
		'admin_dashboard' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		),
		'admin_role' => array(
			'@common_jquery',
		    '@admin_css',
		    '@admin_js',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_modal',
		),
		
		'index' => array(
			'@common_jquery',
		    '@bootstrap_css',
		    '@bootstrap_responsive_css',
		   	'@site_css',
		    '@bootstrap_js_dropdown',
		    '@bootstrap_js_transition',
		    '@bootstrap_js_carousel',
		    '@bootstrap_js_tooltip',
		    '@bootstrap_js_popover',
		    '@bootstrap_js_collapse',
		),
	),
	'routes' => array(),
);
