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

namespace CudiBundle\Form\Admin\Stock\Order;

/**
 * Add Order Directly
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AddDirect extends Add
{
    public function init()
    {
        parent::init();

        $this->remove('article');

        $this->remove('add')
            ->addSubmit('Add', 'stock_add', 'add_order', array(
                'data-help' => '<p>The article will be added to the order queue. This way a group of articles can be ordered for the same supplier.<p>
                    <p>To finish the order, you have to \'place\' it, this can be done by editing the order.</p>',
                'id' => 'stock_add',
            ));
    }
}
