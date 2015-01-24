<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace PromBundle\Controller\Registration;

use Zend\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Mathijs Cuppens
 */

class IndexController extends \PromBundle\Component\Controller\RegistrationController
{
	public function registrationAction()
    {
    	$createForm = $this->getForm('prom_registration_create');
    	$manageForm = $this->getForm('prom_registration_manage');

    	if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData['create']))
            {
	            $createForm->setData($formData);

	            if ($createForm->isValid()) {
	                $createFormData = $createForm->getData();

	                $codeExist = $this->getEntityManager()
	            		->getRepository('PromBundle\Entity\Bus\ReservationCode')
	            		->codeExist($createFormData['create']['ticket_code']);

	                if ($codeExist)
	                {
		        		$this->redirect()->toRoute(
		                    'prom_registration_index',
		                    array(
		                        'action' => 'create',
		                    )
		                );
	        		} else {
	        			return new ViewModel(
				    		array(
				    			'createForm' => $createForm,
				    			'manageForm' => $manageForm,
				    			'status'	 => 'wrong_code',
				    		)
				    	);
	        		}
	        	}
	        }
	        elseif (isset($formData['manage']))
	        {
	        	$manageForm->setData($formData);

	        	if ($createForm->isValid()) {
	                $createFormData = $createForm->getData();

	                $correctEmail = true;

	                if ($codeExist && $correctEmail)
	                {
		        		$this->redirect()->toRoute(
		                    'prom_registration_index',
		                    array(
		                        'action' => 'manage',
		                    )
		                );
	        		} else {
	        			return new ViewModel(
				    		array(
				    			'createForm' => $createForm,
				    			'manageForm' => $manageForm,
				    			'status'	 => 'ERROR',
				    		)
				    	);
	        		}
	        	}
	        }
        }

    	return new ViewModel(
    		array(
    			'createForm' => $createForm,
    			'manageForm' => $manageForm,
    			'status'	 => 'SUCCESS!',
    		)
    	);
    }

    public function createAction()
    {
    	$addForm = $this->getForm('prom_registration_add');

    	return new ViewModel(
    		array(
    			'addForm' => $addForm,
    		)
    	);
    }

    public function manageAction()
    {

    }
}