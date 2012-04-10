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
 
namespace ProfBundle\Controller\Admin;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
	protected function _initConfig()
	{
	    $this->_installConfig(
	        array(
	    	)
	    );
	}
	
	protected function _initAcl()
	{
	    $this->installAcl(
	        array(
	            'profbundle' => array(
	                'admin_action' => array(
	                	'completed', 'confirmArticle', 'manage', 'refused', 'view'
	                ),
	                'prof' => array(
	                    'index'
	                ),
	                'prof_article' => array(
	                    'add', 'delete', 'edit', 'manage', 'typeahead'
	                ),
	                'prof_article_mapping' => array(
	                    'add', 'delete'
	                ),
	                'prof_comment' => array(
	                    'delete', 'manage'
	                ),
	                'prof_file' => array(
	                    'delete', 'download', 'manage', 'progress', 'upload'
	                ),
	                'prof_prof' => array(
	                    'add', 'typeahead'
	                ),
	                'prof_subject' => array(
	                    'manage', 'subject'
	                ),
	            )
	        )
	    );
	    
	    $this->installRoles(
	        array(
	            'prof' => array(
	            	'system' => true,
	                'parents' => array(
	                    'guest',
	                ),
	                'actions' => array(
	                    'prof' => array(
	                        'index'
	                    ),
	                    'prof_article' => array(
	                        'add', 'delete', 'edit', 'manage', 'typeahead'
	                    ),
	                    'prof_article_mapping' => array(
	                        'add', 'delete'
	                    ),
	                    'prof_comment' => array(
	                        'delete', 'manage'
	                    ),
	                    'prof_file' => array(
	                        'delete', 'download', 'manage', 'progress', 'upload'
	                    ),
	                    'prof_prof' => array(
	                        'add', 'typeahead'
	                    ),
	                    'prof_subject' => array(
	                        'manage', 'subject'
	                    ),
	                )
	            )
	        )
	    );
	}
}