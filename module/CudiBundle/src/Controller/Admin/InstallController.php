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
 
namespace CudiBundle\Controller\Admin;

use CudiBundle\Entity\Articles\Options\Binding,
	CudiBundle\Entity\Articles\Options\Color;

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
	            	'key'         => 'search_max_results',
	            	'value'       => '30',
	            	'description' => 'The maximum number of search results shown',
	            ),
			)
		);
		$this->_installBinding();
		$this->_installColor();
	}
	
	protected function _initAcl()
	{
	}
	
	private function _installBinding()
	{
		$bindings = array('Binded');
		
		foreach($bindings as $item) {
			$binding = $this->getEntityManager()
				->getRepository('CudiBundle\Entity\Articles\Options\Binding')
				->findOneByName($item);
			if (null == $binding) {
				$binding = new Binding($item);
				$this->getEntityManager()->persist($binding);
			}
		}
		$this->getEntityManager()->flush();
	}
	
	private function _installColor()
	{
		$colors = array('Red', 'Yellow');
		
		foreach($colors as $item) {
			$color = $this->getEntityManager()
				->getRepository('CudiBundle\Entity\Articles\Options\Color')
				->findOneByName($item);
			if (null == $color) {
				$color = new Color($item);
				$this->getEntityManager()->persist($color);
			}
		}
		$this->getEntityManager()->flush();
	}
}