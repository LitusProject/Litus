<?php

namespace LogisticsBundle\Form\Catalog\Inventory;

/**
 * Book articles
 *
 */
class Inventory extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var array[]
     */
    private $articles = array();

    public function init()
    {
        parent::init();

        foreach ($this->articles as $article) {
            $this->add(
                array(
                    'type'       => 'text',
                    'name'       => 'article-' . $article->getId(),
                    'attributes' => array(
                        'class'       => 'input-very-mini',
                        'style'       => 'float: left; width: 35%; height: 22px; max-width: 50px; min-width: 35px; margin-left:20%',
                        'id'          => 'article-' . $article->getId(),
                        'placeholder' => '0',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                //                                array('name' => 'Digits',),
                                array('name' => 'Int'),
                //                                array(
                //                                    'name'    => 'Between',
                //                                    'options' => array(
                //                                        'min' => 0,
                //                                        'max' => $mapping->getAmountAvailable(),
                //                                    ),
                //                                ),
                            ),
                        ),
                    ),
                )
            );
        }

        $this->addSubmit('Book', 'btn btn-primary pull-right');
    }

    /**
     * @param  array $articles
     * @return self
     */
    public function setArticles(array $articles)
    {
        $this->articles = $articles;

        return $this;
    }
}
