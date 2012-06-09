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
	    'admin_action' => array(
	    	'@common_jquery',
	    	'@admin_css',
	    	'@admin_js',
		    '@common_download_file',
	    ),
	    'prof_index' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@bootstrap_js_alert',
			'@prof_css',
		),
		'prof_subject' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@bootstrap_js_alert',
			'@bootstrap_js_transition',
			'@bootstrap_js_modal',
			'@prof_css',
		),
		'prof_article' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@bootstrap_js_alert',
			'@prof_css',
		),
		'prof_article_mapping' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@bootstrap_js_alert',
			'@common_typeahead_remote',
			'@prof_css',
		),
		'prof_file' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@common_download_file',
		    '@common_permanent_modal',
		    '@common_form_upload_progress',
			'@bootstrap_js_dropdown',
			'@bootstrap_js_alert',
			'@bootstrap_js_transition',
			'@bootstrap_js_modal',
			'@prof_css',
		),
		'prof_comment' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@bootstrap_js_alert',
			'@bootstrap_js_transition',
			'@bootstrap_js_modal',
			'@prof_css',
		),
		'prof_prof' => array(
			'@common_jquery',
			'@bootstrap_css',
			'@bootstrap_js_dropdown',
			'@bootstrap_js_alert',
			'@common_typeahead_remote',
			'@prof_css',
		),
	),
	'routes' => array(),
);