<?php

namespace CudiBundle\Form\Admin\Stock;

use CudiBundle\Entity\Sale\Article;
use LogicException;

/**
 * Update Stock
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Update extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var Article|null
     */
    private $article;

    public function init()
    {
        if ($this->article === null) {
            throw new LogicException('Cannot update the stock of a null article');
        }

        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'number',
                'label'      => 'Number',
                'required'   => true,
                'value'      => $this->article->getStockValue(),
                'attributes' => array(
                    'autocomplete' => 'off',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Int',
                            ),
                            array(
                                'name'    => 'GreaterThan',
                                'options' => array(
                                    'min'       => 0,
                                    'inclusive' => true,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'textarea',
                'name'     => 'comment',
                'label'    => 'Comment',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Update', 'stock_edit', 'updateStock');
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
}
