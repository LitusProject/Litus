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
	    'prof_install' => array(
	    	'@common_jquery',
	    	'@admin_css',
	    	'@admin_js',
	    ),
	    'prof' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@prof_css',
		),
		'prof_subject' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@bootstrap_js_transition',
			'@bootstrap_js_modal',
			'@prof_css',
		),
		'prof_article' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@prof_css',
		),
		'prof_article_mapping' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@common_typeahead_remote',
			'@prof_css',
		),
		'prof_file' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@prof_css',
		),
		'prof_comment' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@bootstrap_js_transition',
			'@bootstrap_js_modal',
			'@prof_css',
		),
		'prof_prof' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@common_typeahead_remote',
			'@prof_css',
		),
	),
	'routes' => array(),
);