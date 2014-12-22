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

namespace CudiBundle\Form\Admin\Stock\Order;

use CudiBundle\Component\Validator\Typeahead\Sale\Article as SaleArticleTypeaheadValidator;

/**
 * Add Order
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var string
     */
    private $barcodePrefix = '';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'article',
            'label'      => 'Article',
            'required'   => true,
            'value'      => $this->barcodePrefix,
            'attributes' => array(
                'id'           => 'article',
                'style'        => 'width: 400px;',
            ),
            'options'    => array(
                'input' => array(
                    'validators'  => array(
                        new SaleArticleTypeaheadValidator($this->getEntityManager()),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'number',
            'label'      => 'Number',
            'required'   => true,
            'attributes' => array(
                'autocomplete' => 'off',
                'id'           => 'order_number',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                        array(
                            'name' => 'greaterthan',
                            'options' => array(
                                'min' => 0,
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'stock_add', 'add', array(
            'data-help' => '<p>The article will be added to the order queue. This way a group of articles can be ordered for the same supplier.<p>
                <p>To finish the order, you have to \'place\' it, this can be done by editing the order.</p>',
            'id'        => 'stock_add',
        ));
    }

    /**
     * @param  string $barcodePrefix
     * @return self
     */
    public function setBarcodePrefix($barcodePrefix)
    {
        $this->barcodePrefix = $barcodePrefix;

        return $this;
    }
}
