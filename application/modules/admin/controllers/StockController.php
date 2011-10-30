<?php

namespace Admin;

use \Doctrine\ORM\EntityManager;

use \Admin\Form\Booking\Add;

use \Litus\Entity\Cudi\Sales\Booking;
use \Litus\FlashMessenger\FlashMessage;

/**
 *
 * This class controlls management of the stock.
 * @author Kristof Mariën
 *
 */
class StockController extends \Litus\Controller\Action
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_forward('manage');
    }
    
    public function manageAction()
	{
        $this->view->stock = $this->getEntityManager()->getRepository('Litus\Entity\Cudi\Stock\StockItem')->findAll();        
    }
}