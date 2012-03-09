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
 
namespace CudiBundle\Controller\Sale;

use CommonBundle\Component\FlashMessenger\FlashMessage,
	CudiBundle\Entity\Sales\ServingQueueItem,
	CudiBundle\Form\Sale\Queue\SignIn as SignInForm;

/**
 * QueueController
 *
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class QueueController extends \CudiBundle\Component\Controller\SaleController
{
	public function overviewAction()
	{
		return array(
			'socketUrl' => $this->getSocketUrl(),
		);
	}

    public function signinAction()
	{
        $form = new SignInForm();
        
        return array(
        	'form' => $form,
        	'socketUrl' => $this->getSocketUrl(),
        );
    }
}