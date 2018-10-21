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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Form\Admin\Invoice;

use BrBundle\Entity\Invoice;

/**
 * Edit a manual invoice.
 *
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 */
class ManualEdit extends ManualAdd
{
    /**
     * @var Invoice
     */
    private $invoice;

    public function init()
    {
        parent::init();

        $this->remove('file');

        $this->add(array(
            'type'       => 'file',
            'name'       => 'file',
            'label'      => 'Change File',
            'required'   => false,
            'attributes' => array(
                'data-help' => 'The file can be of any type and has a filesize limit of ' . self::FILE_SIZE . '.',
                'size'      => 256,
            ),
            'options' => array(
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
        ));

        $this->remove('submit')
            ->addSubmit('Save', 'invoice_edit');

        if (null !== $this->invoice) {
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
