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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Entity\General\Config;

/**
 * RoleController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ConfigController extends \CommonBundle\Component\Controller\ActionController
{
	public function manageAction()
	{
		$configValues = $this->getEntityManager()
			->getRepository('CommonBundle\Entity\General\Config')
			->findAll();
			
		$formattedValues = array();
		foreach($configValues as $entry) {
			if (strstr($entry->getKey(), Config::$separator)) {
				$explodedKey = explode(Config::$separator, $entry->getKey());
				$formattedValues[$explodedKey[0]][$explodedKey[1]] = array(
					'value' => $entry->getValue(),
					'fullKey' => $entry->getKey()
				);
			} else {
				$formattedValues[0][$entry->getKey()] = array(
					'value' => $entry->getValue(),
					'fullKey' => $entry->getKey()
				);
			}
		}
		
		ksort($formattedValues, SORT_STRING);
		
		return array(
			'configValues' => $formattedValues
		);
	}	
	
    public function addAction()
    {
    }

    public function editAction()
    {
    }
}
