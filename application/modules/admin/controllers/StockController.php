<?php

namespace Admin;

use \Litus\FlashMessenger\FlashMessage;

/**
 * This class controls management of the stock.
 * 
 * @author Kristof Mariën <ktistof.marien@litus.cc>
 */
class StockController extends \Litus\Controller\Action
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->_forward('overview');
    }
    
    public function overviewAction()
	{
		$this->view->stock = $this->_createPaginator(
            'Litus\Entity\Cudi\Stock\StockItem'
        );
    }
}