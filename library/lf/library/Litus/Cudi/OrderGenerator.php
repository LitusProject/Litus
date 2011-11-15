<?php

namespace Litus\Cudi;

use \Litus\Entity\Cudi\Stock\Order;

use \Zend\Pdf\PdfDocument;
use \Zend\Pdf\Page as PdfPage;

class OrderGenerator {

    /**
     * @var \Litus\Entity\Cudi\Stock\Order
     */
    private $_order;

	/**
     * @var \Zend\Pdf\PdfDocument
     */
    private $_pdf;

    public function __construct(Order $order)
    {
        $this->_order = $order;
    }

    public function generate()
    {
        $this->_pdf = new PdfDocument();
		$page1 = $this->_pdf->newPage(PdfPage::SIZE_A4);
		$this->_pdf->pages[] = $page1;
    }

	public function render()
	{
		return $this->_pdf->render();
	}
}
