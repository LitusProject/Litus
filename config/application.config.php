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
    'modules' => array(
    	'AsseticBundle',
    	'EdpMarkdown',
    	'MistDoctrine',
    	'ZfTwig',
    	
        'CommonBundle',
        
        /*'BrBundle',
        'CudiBundle',
        'SyllabusBundle',
        
        'CalendarBundle',
        'NewsBundle',
        'PageBundle',*/
    ),
    'module_listener_options' => array( 
        'config_cache_enabled' 	=> false,
        'cache_dir'            	=> 'data/cache',
        'module_paths' 			=> array(
            'module',
            'vendor',
        ),
    ),
);
