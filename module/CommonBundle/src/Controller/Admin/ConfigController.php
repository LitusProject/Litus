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

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Form\Admin\Config\Edit as EditForm,
	CommonBundle\Entity\General\Config,
	Zend\View\Model\ViewModel;

/**
 * ConfigController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ConfigController extends \CommonBundle\Component\Controller\ActionController\AdminController
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
		
		return new ViewModel(
		    array(
    			'configValues' => $formattedValues,
    		)
		);
	}

	public function editAction()
	{
		if (!($entry = $this->_getEntry()))
		    return new ViewModel();
		
        $form = new EditForm(
        	$entry
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
			
            if ($form->isValid($formData)) {
            	$entry->setValue($formData['value']);
            	
            	$this->getEntityManager()->flush();
            	
            	$this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The configuration entry was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_config',
                	array(
                		'action' => 'manage'
                	)
                );
                
                return new ViewModel();
            }
        }
        
        return new ViewModel(
            array(
            	'entry' => $entry,
            	'form' => $form,
            )
        );
	}
		
	public function _getEntry()
	{
		if (null === $this->getParam('key')) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No key was given to identify the configuration entry!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_config',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
	
	    $role = $this->getEntityManager()
	        ->getRepository('CommonBundle\Entity\General\Config')
	        ->findOneByKey($this->getParam('key'));
		
		if (null === $role) {
			$this->flashMessenger()->addMessage(
			    new FlashMessage(
			        FlashMessage::ERROR,
			        'Error',
			        'No configuraiton entry with the given key was found!'
			    )
			);
			
			$this->redirect()->toRoute(
				'admin_config',
				array(
					'action' => 'manage'
				)
			);
			
			return;
		}
		
		return $role;
	}
}
