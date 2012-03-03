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
 
namespace CommonBundle\Component\Controller\ActionController;

/**
 * This abstract function should be implemented by all controller that want to provide
 * installation functionality for a bundle.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class InstallerController extends \CommonBundle\Component\Controller\ActionController
{
	/**
	 * Running all installation methods.
	 *
	 * @return void
	 */
	public function indexAction()
	{
		$this->_initConfig();
		$this->_initAcl();
		
		return array(
			'installerReady' => true
		);
	}
	
	/**
	 * Initiliazes all configuration values for the bundle.
	 *
	 * @return void
	 */
	abstract protected function _initConfig();
	
	/**
	 * Initializes the ACL tree for the bundle.
	 *
	 * @return void
	 */
	abstract protected function _initAcl();
}