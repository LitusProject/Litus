<?php

namespace CudiBundle\Form\Admin\Sale\Article;

use CudiBundle\Entity\Sale\Article;
use DateInterval;

/**
 * Add Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CudiBundle\Hydrator\Sale\Article';

    /**
     * @var Article|null
     */
    protected $article;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'purchase_price',
                'label'      => 'Purchase Price',
                'required'   => true,
                'attributes' => array(
                    'id' => 'purchase_price',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Price'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'sell_price',
                'label'      => 'Sell Price',
                'required'   => true,
                'attributes' => array(
                    'id' => 'sell_price',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Price'),
                        ),
                    ),
                ),
            )
        );

        $barcodeCheck = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_sale_article_barcode_check') == 1;

        $barcodeInput = array();
        if ($barcodeCheck) {
            $barcodeInput = array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'SaleArticleBarcodeUnique',
                        'options' => array(
                            'sale_article' => $this->article,
                        ),
                    ),
                    array(
                        'name'    => 'barcode',
                        'options' => array(
                            'adapter'     => 'Ean12',
                            'useChecksum' => false,
                        ),
                    ),
                ),
            );
        }

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'barcode',
                'label'      => 'Barcode',
                'required'   => $barcodeCheck,
                'attributes' => array(
                    'class'     => 'disableEnter',
                    'data-help' => 'This is the main barcode of the article. This one will be printed on the front page.',
                ),
                'options'    => array(
                    'input' => $barcodeInput,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'supplier',
                'label'      => 'Supplier',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->getSuppliers(),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'bookable',
                'label'      => 'Bookable',
                'attributes' => array(
                    'data-help' => 'Enabling this option will allow students to book this article.',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'unbookable',
                'label'      => 'Cancelable',
                'attributes' => array(
                    'data-help' => 'Enabling this option will allow students with bookings of this article to cancel their reservation.',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'sellable',
                'label'      => 'Sellable',
                'value'      => true,
                'attributes' => array(
                    'data-help' => 'Enabling this option will allow to sell this article in the \'Sale App\'.',
                ),
            )
        );

        $dateinterval = new DateInterval(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.reservation_expire_time')
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'can_expire',
                'label'      => 'Can Expire',
                'attributes' => array(
                    'data-help' => 'Enabling this option will expire the bookings of this article after a period of ' . $dateinterval->format('%d days'),
                ),
            )
        );

        $this->addSubmit('Add', 'article_add');

        if ($this->article !== null) {
            $this->bind($this->article);
        }
    }

    /**
     * @param  Article $article
     * @return self
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;

        return $this;
    }

    private function getSuppliers()
    {
        $suppliers = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();
        $supplierOptions = array();
        foreach ($suppliers as $item) {
            $supplierOptions[$item->getId()] = $item->getName();
        }

        return $supplierOptions;
    }
}
