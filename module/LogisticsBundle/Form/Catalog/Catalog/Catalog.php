<?php

namespace LogisticsBundle\Form\Catalog\Catalog;

/**
 * Book articles
 *
 */
class Catalog extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var array[]
     */
    private $articles = array();

    public function init()
    {
        parent::init();

        foreach ($this->articles as $article) {
            $mapping = $article['article'];

            $this->add(
                array(
                    'type'       => 'hidden',
                    'name'       => 'article-' . $mapping->getId(),
                    'attributes' => array(
                        'class'       => 'input-very-mini',
                        'id'          => 'article-' . $mapping->getId(),
                        'placeholder' => '0',
                    ),
                    'options' => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name' => 'Digits',
                                ),
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
