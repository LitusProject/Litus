<?php

namespace BrBundle\Form\Admin\Invoice;

use BrBundle\Entity\Invoice;

/**
 * Edit a manual invoice.
 *
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 */
class ManualEdit extends \BrBundle\Form\Admin\Invoice\ManualAdd
{
    /**
     * @var Invoice
     */
    private $invoice;

    public function init()
    {
        parent::init();

        $this->remove('file');

        $this->add(
            array(
                'type'       => 'file',
                'name'       => 'file',
                'label'      => 'Change File',
                'required'   => false,
                'attributes' => array(
                    'data-help' => 'The file can be of any type and has a filesize limit of ' . self::FILE_SIZE . '.',
                    'size'      => 256,
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'FileExtension',
                                'options' => array(
                                    'extension' => 'pdf',
                                ),
                            ),
                            array(
                                'name'    => 'FileSize',
                                'options' => array(
                                    'max' => self::FILE_SIZE,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->remove('submit')
            ->addSubmit('Save', 'invoice_edit');

        if ($this->invoice !== null) {
            $this->bind($this->invoice);
        }
    }

    /**
     * @param  Invoice $invoice
     * @return self
     */
    public function setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }
}
