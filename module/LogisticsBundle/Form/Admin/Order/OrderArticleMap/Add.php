<?php

namespace LogisticsBundle\Form\Admin\Order\OrderArticleMap;

/**
 * Add an OrderArticleMap
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var array All possible articles
     */
    private $articles;

    protected $hydrator = 'LogisticsBundle\Hydrator\Order\OrderArticleMap';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'articles',
                'label'      => 'Articles',
                'required'   => true,
                'attributes' => array(
                    'multiple' => true,
                    'style'    => 'max-width: 100%;height: 300px;',
                    'options'  => $this->getArticleNames(),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }

    /**
     * @return array
     */
    private function getArticleNames()
    {
        $articleNames = array();
        foreach ($this->articles as $article) {
            $articleNames[$article->getId()] = $article->getCategory() . ' - ' . $article->getName();
        }

        return $articleNames;
    }

    /**
     * @param  array All possible articles
     * @return self
     */
    public function setArticles(array $articles)
    {
        $this->articles = $articles;

        return $this;
    }
}
