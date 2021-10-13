<?php

namespace BrBundle\Form\Admin\Product;

/**
 * Edit a product.
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \BrBundle\Form\Admin\Product\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'product_edit');
    }
}
