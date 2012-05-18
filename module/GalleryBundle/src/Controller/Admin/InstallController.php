<?php
 
namespace GalleryBundle\Controller\Admin;

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
				array(
					'key'         => 'gallery_path',
					'value'       => '/_gallery/albums',
					'description' => 'The path to the gallery albums',
				)
			)
		);
	}
	
	protected function _initAcl()
	{
	    $this->installAclStructure(
	        array(
	            'galleryBundle' => array(
	            )
	        )
	    );
	    
	    $this->installRoles(
	        array(
    	        'guest' => array(
    	            'parent_roles' => array(),
    	            'actions' => array(
    	            )
    	        ),
    	        'sudo' => array(
    	            'parent_roles' => array(
    	                'guest'
    	            ),
    	            'actions' => array(
    	            )
    	        )
    	    )
    	);
	}
}